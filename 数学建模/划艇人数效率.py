import numpy as np
import matplotlib.pyplot as plt

# 数据准备
n = np.array([1, 2, 3, 4])  # 人数
t = np.array([7.21, 6.88, 6.32, 5.84])  # 平均成绩

# 对数变换使方程线性化
# ln(t) = ln(α) + β*ln(n)
x = np.log(n)
y = np.log(t)

# 使用最小二乘法求解参数
beta = (np.mean(x*y) - np.mean(x)*np.mean(y)) / (np.mean(x*x) - np.mean(x)**2)
ln_alpha = np.mean(y) - beta*np.mean(x)
alpha = np.exp(ln_alpha)

# 打印结果
print(f'α = {alpha:.4f}')
print(f'β = {beta:.4f}')

# 生成拟合曲线的点
n_fit = np.linspace(1, 6, 100)
t_fit = alpha * n_fit**beta

# 设置中文字体
plt.rcParams['font.sans-serif'] = ['SimHei']  # 用来正常显示中文标签
plt.rcParams['axes.unicode_minus'] = False  # 用来正常显示负号


# 绘图
plt.figure(figsize=(10, 6))
plt.scatter(n, t, color='red', label='实际数据')
plt.plot(n_fit, t_fit, color='blue', label='拟合曲线')
plt.xlabel('人数 n')
plt.ylabel('成绩 t (min)')
plt.title('赛艇比赛成绩与人数的关系')
plt.grid(True)
plt.legend()
plt.show()