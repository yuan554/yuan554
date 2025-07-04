import numpy as np
import matplotlib.pyplot as plt

# 设置中文字体
plt.rcParams['font.sans-serif'] = ['SimHei']  
plt.rcParams['axes.unicode_minus'] = False  

# 数据准备
v = np.array([20, 40, 60, 80, 100, 120, 140])  # 车速
d = np.array([6.5, 17.8, 33.6, 57.1, 83.4, 118.0, 153.5])  # 制动距离

# 构建方程组 d = c₁v + c₂v²
A = np.vstack([v, v**2]).T  # 系数矩阵
c = np.linalg.lstsq(A, d, rcond=None)[0]  # 求解c₁和c₂

print(f'c₁ = {c[0]:.4f}')
print(f'c₂ = {c[1]:.4f}')

# 生成拟合曲线的点
v_fit = np.linspace(0, 150, 100)
d_fit = c[0]*v_fit + c[1]*v_fit**2

# 绘图
plt.figure(figsize=(10, 6))
plt.scatter(v, d, color='red', label='实际数据')
plt.plot(v_fit, d_fit, 'b-', label='拟合曲线')

plt.xlabel('车速 v (km/h)')
plt.ylabel('制动距离 d (m)')
plt.title('车速与制动距离关系图')
plt.grid(True)
plt.legend()
plt.show()