import numpy as np
import pandas as pd
import matplotlib.pyplot as plt
from sklearn.ensemble import GradientBoostingRegressor
from sklearn.preprocessing import StandardScaler
from sklearn.pipeline import make_pipeline
from sklearn.metrics import mean_squared_error, r2_score
from statsmodels.tsa.arima.model import ARIMA

# 设置matplotlib支持中文显示
plt.rcParams['font.sans-serif'] = ['SimHei', 'Microsoft YaHei', 'DejaVu Sans']
plt.rcParams['axes.unicode_minus'] = False

# 1. 数据整合与特征工程
# 出生人口数据 (1990-2024)
years_birth = np.arange(1990, 2025)
birth_counts = np.array([2391,2258,2119,2126,2104,2063,2067,2038,1991,1909,1771,1702,1647,1599,
                        1593,1617,1584,1594,1608,1615,1588,1604,1635,1640,1687,1655,1883,1765,1523,
                        1465,1200,1062,956,902,954])

# 高校毕业生数据 (2012-2024)
grad_years = np.arange(2012, 2025)
grad_counts = np.array([624.7, 638.7, 659.4, 680.9, 704.2, 735.8, 753.3087, 
                        758.5298, 797.1991, 826.5064, 967.2565, 1047.0258, 1059.3802])

# 经济数据 (2005-2024)
econ_years = np.arange(2005, 2025)
gdp_data = np.array([189907.5, 222578.4, 274179.7, 324317.8, 354521.6, 419253.3, 495707.6, 
                     547510.6, 603660.4, 655782.9, 702511.5, 761193, 847382.9, 936010.1, 
                     1005872.4, 1034867.6, 1173823, 1234029.4, 1294271.7, 1349083.5])
per_capita_gdp = np.array([14567, 16977, 20805, 24483, 26631, 31341, 36855, 
                           40431, 44281, 47802, 50912, 54849, 60691, 66726, 
                           71453, 73338, 83111, 87385, 91746, 95749])

# 创建主数据集
data = pd.DataFrame({
    'Year': grad_years,
    'Graduates': grad_counts
})

# 添加人口特征
data['Birth_22_years_ago'] = [birth_counts[np.where(years_birth == year-22)[0][0]] for year in grad_years]
data['Birth_16_years_ago'] = [birth_counts[np.where(years_birth == year-16)[0][0]] for year in grad_years]  # 高中毕业生影响

# 添加经济特征（使用GDP和人均GDP）
def get_econ_data(year):
    idx = np.where(econ_years == year)[0]
    if len(idx) > 0:
        return gdp_data[idx[0]], per_capita_gdp[idx[0]]
    # 对于2024年，使用最新数据
    return gdp_data[-1], per_capita_gdp[-1]

data[['GDP', 'Per_Capita_GDP']] = data['Year'].apply(
    lambda year: pd.Series(get_econ_data(year))
)

# 添加教育特征（来自年度数据 (4).xls）
# 普通本科招生数（万人）
undergrad_enrollment = np.array([236.3647, 253.0854, 282.0971, 297.0601, 326.1081, 351.2563, 
                                 356.6411, 374.0574, 381.4331, 383.4152, 389.4184, 405.4007, 
                                 410.7534, 422.159, 431.288, 443.1154, 444.5969, 467.9358, 
                                 478.1609])
# 对齐年份（2005-2023）
enrollment_years = np.arange(2005, 2024)
data['Undergrad_Enrollment'] = data['Year'].apply(
    lambda year: undergrad_enrollment[np.where(enrollment_years == year)[0][0]] if year in enrollment_years else np.nan
)
# 填充缺失值（2024年）
data.loc[data['Year'] == 2024, 'Undergrad_Enrollment'] = 478.1609 * 1.02  # 2%增长假设

# 添加教育投入特征（GDP中教育占比估算）
# 中国教育经费一般占GDP的4%左右
data['Education_Investment'] = data['GDP'] * 0.04

# 添加政策变化标志（2022年扩招政策）
data['Policy_Change'] = (data['Year'] >= 2022).astype(int)

# 添加时间趋势
data['Year_Index'] = data['Year'] - 2011

# 2. 预测未来数据（2025-2050）
# 预测未来出生人口（使用ARIMA模型）
birth_model = ARIMA(birth_counts, order=(2,1,1))
birth_fit = birth_model.fit()
birth_forecast = birth_fit.get_forecast(steps=26).predicted_mean
future_birth = np.concatenate([birth_counts, birth_forecast])
future_birth_years = np.arange(1990, 2051)

# 预测未来GDP（使用ARIMA模型）
gdp_model = ARIMA(gdp_data, order=(1,1,1))
gdp_fit = gdp_model.fit()
gdp_forecast = gdp_fit.get_forecast(steps=26).predicted_mean
future_gdp = np.concatenate([gdp_data, gdp_forecast])
future_gdp_years = np.arange(2005, 2051)

# 预测未来人均GDP（基于历史趋势）
per_capita_trend = np.polyfit(econ_years, per_capita_gdp, 2)
future_per_capita = np.polyval(per_capita_trend, np.arange(2005, 2051))

# 预测本科招生数（基于历史趋势）
enrollment_trend = np.polyfit(enrollment_years, undergrad_enrollment, 2)
future_enrollment = np.polyval(enrollment_trend, np.arange(2005, 2051))

# 3. 构建GBRT模型
# 特征选择
features = [
    'Birth_22_years_ago', 
    'Birth_16_years_ago',
    'GDP',
    'Per_Capita_GDP',
    'Undergrad_Enrollment',
    'Education_Investment',
    'Year_Index',
    'Policy_Change'
]

# 准备训练数据
train_data = data[data['Year'] <= 2020]  # 使用2020年及之前数据训练
X_train = train_data[features]
y_train = train_data['Graduates']

# 创建GBRT模型
gbrt_model = GradientBoostingRegressor(
    n_estimators=800,
    learning_rate=0.03,
    max_depth=5,
    min_samples_split=5,
    random_state=42,
    subsample=0.8
)

# 训练模型
gbrt_model.fit(X_train, y_train)

# 评估模型（在完整历史数据上）
X_full = data[features]
y_full = data['Graduates']
y_pred = gbrt_model.predict(X_full)
rmse = np.sqrt(mean_squared_error(y_full, y_pred))
r2 = r2_score(y_full, y_pred)
print(f"模型评估 - RMSE: {rmse:.2f}万, R²: {r2:.4f}")

# 4. 预测2025-2050年毕业生人数
future_years = np.arange(2025, 2051)
future_data = []

for year in future_years:
    # 人口特征
    birth_22 = future_birth[np.where(future_birth_years == year-22)[0][0]]
    birth_16 = future_birth[np.where(future_birth_years == year-16)[0][0]]
    
    # 经济特征
    gdp = future_gdp[np.where(future_gdp_years == year)[0][0]]
    per_capita = future_per_capita[np.where(future_gdp_years == year)[0][0]]
    
    # 教育特征
    enrollment = future_enrollment[np.where(future_gdp_years == year)[0][0]]
    edu_investment = gdp * 0.04  # 保持4%占比
    
    # 其他特征
    year_idx = year - 2011
    policy = 1  # 假设政策持续
    
    future_data.append([
        birth_22, birth_16, gdp, per_capita, enrollment, 
        edu_investment, year_idx, policy
    ])

# 创建预测DataFrame
X_future = pd.DataFrame(future_data, columns=features)
future_predictions = gbrt_model.predict(X_future)

# 5. 可视化结果
plt.figure(figsize=(18, 10))

# 历史数据
plt.plot(data['Year'], data['Graduates'], 'bo-', linewidth=2.5, markersize=8, label='历史数据')

# 预测数据
plt.plot(future_years, future_predictions, 'r--', linewidth=2.5, label='GBRT预测')

# 添加预测区间（基于模型不确定性）
std_error = np.std(y_train - gbrt_model.predict(X_train))
lower_bound = future_predictions - 1.96 * std_error
upper_bound = future_predictions + 1.96 * std_error
plt.fill_between(future_years, lower_bound, upper_bound, color='r', alpha=0.2, label='95%置信区间')

# 标记关键年份
key_years = [2025, 2030, 2040, 2050]
for year in key_years:
    if year <= 2024:
        value = data[data['Year'] == year]['Graduates'].values[0]
    else:
        idx = np.where(future_years == year)[0][0]
        value = future_predictions[idx]
    
    plt.scatter(year, value, color='purple', s=120, zorder=5)
    plt.annotate(f'{year}: {value:.0f}万', (year, value), 
                 textcoords="offset points", xytext=(0,15), 
                 ha='center', fontsize=11, fontweight='bold',
                 bbox=dict(boxstyle="round,pad=0.3", fc="white", ec="gray", alpha=0.8))

# 添加政策变化标记
plt.axvline(x=2022, color='g', linestyle='--', alpha=0.7)
plt.text(2022.2, 500, '2022年扩招政策', rotation=90, va='bottom', fontsize=10, color='g')

plt.title('高校毕业生人数预测(2012-2050) - 含经济因素分析', fontsize=18)
plt.xlabel('年份', fontsize=14)
plt.ylabel('毕业生人数(万)', fontsize=14)
plt.legend(fontsize=12, loc='upper left')
plt.grid(True, linestyle='--', alpha=0.7)
plt.xticks(np.arange(2010, 2051, 5), rotation=45)
plt.ylim(500, 1500)
plt.tight_layout()

# 添加数据来源说明
plt.figtext(0.5, 0.01, "数据来源：国家统计局 | 预测模型：GBRT（梯度提升回归树）", 
            ha="center", fontsize=10, alpha=0.7)
plt.show()

# 6. 特征重要性分析
feature_importance = pd.DataFrame({
    'Feature': features,
    'Importance': gbrt_model.feature_importances_
}).sort_values('Importance', ascending=False)

plt.figure(figsize=(12, 7))
plt.barh(feature_importance['Feature'], feature_importance['Importance'], color='skyblue')
plt.xlabel('特征重要性', fontsize=12)
plt.title('GBRT模型特征重要性分析', fontsize=16)
plt.gca().invert_yaxis()
for i, v in enumerate(feature_importance['Importance']):
    plt.text(v + 0.01, i, f'{v:.3f}', va='center', fontsize=10)
plt.tight_layout()
plt.show()

# 7. 预测结果表格
future_results = pd.DataFrame({
    'Year': future_years,
    'Predicted_Graduates': future_predictions,
    'Birth_Year': future_years - 22,
    'Birth_Count': [future_birth[np.where(future_birth_years == year-22)[0][0]] for year in future_years],
    'GDP': future_gdp[-26:],
    'Per_Capita_GDP': future_per_capita[-26:]
})

print("\n高校毕业生预测结果(2025-2050):")
print(future_results[['Year', 'Predicted_Graduates', 'Birth_Year', 'Birth_Count', 'GDP', 'Per_Capita_GDP']].round(1))

# 8. 经济与毕业生关系分析
fig, ax1 = plt.subplots(figsize=(15, 7))

# 毕业生数据
ax1.plot(data['Year'], data['Graduates'], 'bo-', label='历史毕业生数')
ax1.plot(future_years, future_predictions, 'r--', label='预测毕业生数')
ax1.set_xlabel('年份')
ax1.set_ylabel('毕业生数(万)', color='b')
ax1.tick_params(axis='y', labelcolor='b')
ax1.set_ylim(500, 1500)
ax1.legend(loc='upper left')

# 经济数据
ax2 = ax1.twinx()
ax2.plot(data['Year'], data['Per_Capita_GDP']/1000, 'g-', label='人均GDP(千元)')
ax2.plot(future_years, future_results['Per_Capita_GDP']/1000, 'm--', label='预测人均GDP(千元)')
ax2.set_ylabel('人均GDP(千元)', color='g')
ax2.tick_params(axis='y', labelcolor='g')
ax2.legend(loc='upper right')

plt.title('高校毕业生人数与经济发展关系(2012-2050)', fontsize=16)
plt.grid(True, linestyle='--', alpha=0.7)
plt.tight_layout()
plt.show()

# 9. 模型验证和敏感性分析
print("\n" + "="*60)
print("模型验证和敏感性分析")
print("="*60)

# 交叉验证
from sklearn.model_selection import cross_val_score
cv_scores = cross_val_score(gbrt_model, X_train, y_train, cv=5, scoring='neg_mean_squared_error')
cv_rmse = np.sqrt(-cv_scores)
print(f"5折交叉验证RMSE: {cv_rmse.mean():.2f} ± {cv_rmse.std():.2f}")

# 敏感性分析 - 不同政策情景
print("\n敏感性分析 - 不同政策情景:")
scenarios = {
    '保守情景': 0.8,  # 政策效果减弱20%
    '基准情景': 1.0,  # 当前预测
    '积极情景': 1.2   # 政策效果增强20%
}

for scenario_name, factor in scenarios.items():
    # 调整政策影响
    adjusted_predictions = future_predictions * factor
    print(f"{scenario_name} - 2050年预测: {adjusted_predictions[-1]:.0f}万")

# 10. 升学率分析
enrollment_rate = data['Graduates'] / data['Birth_22_years_ago']
future_enrollment_rate = future_predictions / future_results['Birth_Count']

plt.figure(figsize=(14, 7))
plt.plot(data['Year'], enrollment_rate * 100, 'bo-', label='历史升学率')
plt.plot(future_years, future_enrollment_rate * 100, 'r--', label='预测升学率')
plt.axhline(y=enrollment_rate.mean() * 100, color='g', linestyle='--', alpha=0.7, label='历史平均升学率')
plt.xlabel('年份')
plt.ylabel('升学率(%)')
plt.title('高校毕业生升学率趋势分析(2012-2050)')
plt.legend()
plt.grid(True, linestyle='--', alpha=0.7)
plt.tight_layout()
plt.show()

print(f"\n升学率分析:")
print(f"历史平均升学率: {enrollment_rate.mean()*100:.1f}%")
print(f"2050年预测升学率: {future_enrollment_rate[-1]*100:.1f}%")
print(f"升学率变化趋势: {'上升' if future_enrollment_rate[-1] > enrollment_rate.mean() else '下降'}")

# 11. 保存预测结果
future_results.to_csv('高校毕业生预测结果_2025-2050.csv', index=False, encoding='utf-8-sig')
print(f"\n预测结果已保存到: 高校毕业生预测结果_2025-2050.csv")