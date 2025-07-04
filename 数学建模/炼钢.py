import numpy as np
import matplotlib.pyplot as plt

# 设置中文字体
plt.rcParams['font.sans-serif'] = ['SimHei']
plt.rcParams['axes.unicode_minus'] = False

# 数据准备
x = np.array([2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16])
y = np.array([6.42, 8.20, 9.58, 9.50, 9.70, 10.0, 9.93, 9.99, 10.49, 10.59, 10.6, 10.8, 10.6, 10.9, 10.76])

# 构建方程 1/y = a + b/x
X = np.vstack([np.ones_like(x), 1/x]).T  # 系数矩阵
Y = 1/y  # 因变量

# 使用最小二乘法求解参数
params = np.linalg.lstsq(X, Y, rcond=None)[0]
a, b = params

print(f'a = {a:.6f}')
print(f'b = {b:.6f}')

# 生成拟合曲线
x_fit = np.linspace(1, 17, 100)
y_fit = 1/(a + b/x_fit)

# 绘图
plt.figure(figsize=(12, 6))

# 绘制原始数据点
plt.scatter(x, y, color='red', label='实际数据')

# 绘制拟合曲线
plt.plot(x_fit, y_fit, 'b-', label='拟合曲线')

plt.xlabel('使用次数 x')
plt.ylabel('容积增大量 y')
plt.title('钢包使用次数与容积增大量的关系')
plt.grid(True)
plt.legend()
plt.show()

# 计算拟合优度 R²
y_mean = np.mean(y)
ss_tot = np.sum((y - y_mean)**2)
y_pred = 1/(a + b/x)
ss_res = np.sum((y - y_pred)**2)
r2 = 1 - (ss_res / ss_tot)
print(f'拟合优度 R² = {r2:.4f}')