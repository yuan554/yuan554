import numpy as np
import matplotlib.pyplot as plt

# 设置中文字体
plt.rcParams['font.sans-serif'] = ['SimHei']
plt.rcParams['axes.unicode_minus'] = False

# 数据准备
years = np.array(range(1900, 1926))
Q = np.array([1.05, 1.18, 1.29, 1.30, 1.30, 1.42, 1.50, 1.52, 1.46, 1.60, 1.69, 1.81, 1.93,
              1.95, 2.01, 2.00, 2.09, 1.96, 2.20, 2.12, 2.16, 2.08, 2.24, 2.56, 2.34, 2.45])
K = np.array([1.04, 1.06, 1.16, 1.22, 1.27, 1.37, 1.44, 1.53, 1.57, 2.05, 2.51, 2.63, 2.74,
              2.82, 3.24, 3.24, 3.61, 4.10, 4.36, 4.77, 4.75, 4.54, 4.58, 4.58, 4.58, 4.58])
L = np.array([1.05, 1.08, 1.18, 1.22, 1.17, 1.30, 1.39, 1.47, 1.31, 1.43, 1.58, 1.59, 1.66,
              1.68, 1.65, 1.62, 1.86, 1.93, 1.96, 1.95, 1.90, 1.58, 1.67, 1.82, 1.60, 1.61])

# 对方程取对数: ln(Q) = ln(a) + αln(K) + βln(L)
ln_Q = np.log(Q)
ln_K = np.log(K)
ln_L = np.log(L)

# 构建方程组
X = np.vstack([np.ones_like(ln_K), ln_K, ln_L]).T
params = np.linalg.lstsq(X, ln_Q, rcond=None)[0]

# 获取参数值
ln_a, alpha, beta = params
a = np.exp(ln_a)

print(f'估计的参数值：')
print(f'a = {a:.4f}')
print(f'α = {alpha:.4f}')
print(f'β = {beta:.4f}')

# 计算拟合值
Q_fit = a * (K**alpha) * (L**beta)

# 计算R²
R2 = 1 - np.sum((Q - Q_fit)**2) / np.sum((Q - np.mean(Q))**2)
print(f'R² = {R2:.4f}')

# 绘图
plt.figure(figsize=(12, 6))
plt.plot(years, Q, 'ro-', label='实际产值')
plt.plot(years, Q_fit, 'b--', label='拟合产值')
plt.xlabel('年份')
plt.ylabel('产值')
plt.title('Cobb-Douglas生产函数拟合结果')
plt.legend()
plt.grid(True)
plt.show()

# 验证α+β是否小于1
print(f'α + β = {alpha + beta:.4f}')