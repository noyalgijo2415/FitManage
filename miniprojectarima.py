
import os
import pandas as pd
import numpy as np
import matplotlib.pyplot as plt
import seaborn as sns
from statsmodels.tsa.arima.model import ARIMA
from pmdarima.arima import auto_arima
from statsmodels.tsa.stattools import acf, pacf
from statsmodels.graphics.tsaplots import plot_acf, plot_pacf
from sklearn.metrics import mean_squared_error
import warnings

warnings.filterwarnings("ignore")

# 🔹 Step 1: Load the Dataset
file_path = r"C:\Users\TUF\Downloads\checkin_checkout_history_updated.csv"  # Update path if needed
df = pd.read_csv(file_path, parse_dates=['checkin_time'])

# 🔹 Step 2: Filter Data for Gym_3
df_gym3 = df[df['gym_id'] == 'gym_3'].copy()  # Ensure 'gym_id' column exists

# 🔹 Step 3: Extract Date and Time Slots
df_gym3['date'] = df_gym3['checkin_time'].dt.date
df_gym3['hour'] = df_gym3['checkin_time'].dt.hour

# Define time slots
def assign_time_slot(hour):
    if 5 <= hour < 12:
        return 'Morning'
    elif 12 <= hour < 17:
        return 'Afternoon'
    elif 17 <= hour < 21:
        return 'Evening'
    else:
        return 'Night'

df_gym3['time_slot'] = df_gym3['hour'].apply(assign_time_slot)

# 🔹 Step 4: Aggregate Check-ins Per Time Slot
df_grouped = df_gym3.groupby(['date', 'time_slot']).size().reset_index(name='logins')

# Convert 'date' to datetime for time series modeling
df_grouped['date'] = pd.to_datetime(df_grouped['date'])

# 🔹 Step 5: Pivot Data (Time Slot Check-ins for Each Date)
df_pivot = df_grouped.pivot(index='date', columns='time_slot', values='logins').fillna(0)

# 🔹 Step 6: Set Date as Time Series Index
df_pivot.index = pd.date_range(start=df_pivot.index.min(), periods=len(df_pivot), freq='D')

# Aggregating hourly check-in counts
hourly_checkin_counts = df_gym3.groupby('hour').size().reset_index(name='checkin_count')


# 🔹 Extract Weekday
df_gym3['weekday'] = df_gym3['checkin_time'].dt.day_name()

# 🔹 Count Check-ins Per Weekday
weekday_counts = df_gym3['weekday'].value_counts()
weekday_order = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"]

# 📊 Bar Chart: Weekday Popularity
plt.figure(figsize=(10, 5))
sns.barplot(x=weekday_counts.index, y=weekday_counts.values, order=weekday_order, palette='viridis')
plt.title("📅 Gym 3 - Check-ins by Weekday")
plt.xlabel("Day of the Week")
plt.ylabel("Number of Check-ins")
plt.xticks(rotation=45)
plt.grid(axis='y', linestyle='--', alpha=0.7)
plt.show()


# Plot the hourly check-in distribution
plt.figure(figsize=(12, 6))
sns.lineplot(x='hour', y='checkin_count', data=hourly_checkin_counts, marker='o', color='skyblue')
plt.title('📊 Hourly Check-in Distribution for Gym 3')
plt.xlabel('⏰ Hour of the Day')
plt.ylabel('🔢 Check-in Count')
plt.xticks(range(24))
plt.grid(True, linestyle='--', alpha=0.7)
plt.show()

time_slot_counts = df_gym3['time_slot'].value_counts()
# 📊 Pie Chart: Time Slot Distribution
plt.figure(figsize=(7, 7))
colors = ['#ff9999', '#66b3ff', '#99ff99', '#ffcc99']
plt.pie(time_slot_counts, labels=time_slot_counts.index, autopct='%1.1f%%', colors=colors, startangle=140)
plt.title("⏰ Time Slot Distribution at Gym_3")
plt.show()
# 🔹 Categorize Users Based on Visit Frequency
user_visit_counts = df_gym3.groupby('user_id').size()

def categorize_user(visits):
    if visits == 1:
        return "One-time"
    elif visits <= 5:
        return "Occasional"
    elif visits <= 15:
        return "Regular"
    else:
        return "Frequent"

df_gym3['user_category'] = df_gym3['user_id'].map(lambda x: categorize_user(user_visit_counts[x]))

# 📊 Bar Chart: User Categories
user_category_counts = df_gym3['user_category'].value_counts()
plt.figure(figsize=(8, 5))
sns.barplot(x=user_category_counts.index, y=user_category_counts.values, palette='coolwarm')
plt.title("🏋️‍♂️ Gym_3 User Categories")
plt.xlabel("User Category")
plt.ylabel("Number of Users")
plt.show()




# 🔹 Step 7: Visualize Check-in Trends
df_gym3.groupby('date')['checkin_time'].count().plot(figsize=(12,6), marker='o', title="📅 Daily Check-in Trends at Gym_3")
plt.xlabel("Date")
plt.ylabel("Number of Check-ins")
plt.grid()
plt.show()

# 🔹 Step 8: ACF & PACF Analysis
plt.figure(figsize=(12,5))
plot_acf(df_pivot.mean(axis=1), lags=20)  # Autocorrelation
plt.show()

plt.figure(figsize=(12,5))
plot_pacf(df_pivot.mean(axis=1), lags=20)  # Partial Autocorrelation
plt.show()

# 🔹 Step 9: Train ARIMA Model for Each Time Slot
def train_arima(series, steps=16):  # Predict next 16 days
    model_auto = auto_arima(series, seasonal=False, trace=True)
    order = model_auto.order  # Get best (p,d,q)

    model = ARIMA(series, order=order)
    result = model.fit()
    
    # Forecast next `steps` days
    forecast = result.forecast(steps=steps)
    
    return forecast, result

# 🔹 Step 10: Predict Future Logins for Each Time Slot
future_dates = pd.date_range(df_pivot.index[-1] + pd.Timedelta(days=1), periods=16, freq='D')
forecast_results = {}
rms_errors = {}

for slot in df_pivot.columns:
    print(f"🔮 Training ARIMA for {slot}...")
    forecast_results[slot], model_result = train_arima(df_pivot[slot])

    # Calculate RMSE
    train_size = int(len(df_pivot[slot]) * 0.8)
    train, test = df_pivot[slot][:train_size], df_pivot[slot][train_size:]
    test_predictions = model_result.predict(start=len(train), end=len(train) + len(test) - 1)
    
    rmse = np.sqrt(mean_squared_error(test, test_predictions))
    rms_errors[slot] = rmse

    # Plot Forecast
    plt.figure(figsize=(10,5))
    plt.plot(df_pivot[slot], label="Actual Logins", marker="o")
    plt.plot(future_dates, forecast_results[slot], label="Forecast", marker="x", linestyle="dashed")
    plt.xlabel("Date")
    plt.ylabel("Predicted Logins")
    plt.title(f"📊 Login Forecast for {slot} at Gym_3")
    plt.legend()
    plt.grid()
    plt.show()

# 🔹 Step 11: Combine Future Predictions
forecast_df = pd.DataFrame(forecast_results, index=future_dates)
print("\n🔮 Future Peak Hour Predictions at Gym_3:\n", forecast_df)


df_gym3['weekday'] = df_gym3['checkin_time'].dt.day_name()  # Extract weekday name




pivot_table = df_gym3.pivot_table(index=df_gym3['hour'], columns=df_gym3['weekday'], values='checkin_time', aggfunc='count').fillna(0)

plt.figure(figsize=(10,6))
sns.heatmap(pivot_table, cmap="coolwarm", annot=True, fmt=".0f", linewidths=0.5)
plt.title("🔥 Heatmap of Check-ins by Hour and Weekday at Gym_3")
plt.xlabel("Weekday")
plt.ylabel("Hour of the Day")
plt.show()


top_members = df_gym3['user_id'].value_counts().head(10)
top_members.plot(kind='bar', color='purple')
plt.title("🏋️‍♂️ Top 10 Most Frequent Gym Members")
plt.xlabel("User ID")
plt.ylabel("Check-in Count")
plt.show()




# 🔹 Step 12: Display RMSE Values
print("\n📉 Root Mean Square Error (RMSE) for each Time Slot:\n", rms_errors)
from statsmodels.tsa.stattools import adfuller

# Function to check stationarity
def check_stationarity(series):
    result = adfuller(series.dropna())  # Drop NaN values
    print("\n🔍 ADF Test Results for:", series.name)
    print(f"ADF Statistic: {result[0]:.4f}")
    print(f"p-value: {result[1]:.4f}")
    
    if result[1] < 0.05:
        print("✅ The data is stationary (Reject H0)")
    else:
        print("❌ The data is non-stationary (Fail to Reject H0)")

# Check stationarity for each time slot
for slot in df_pivot.columns:
    check_stationarity(df_pivot[slot])
