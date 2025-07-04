from PIL import Image
import numpy as np
import matplotlib.pyplot as plt
from pathlib import Path

# 使用 pathlib 处理路径
image_path = Path(r"C:\Users\yuan\Pictures\壁纸2.jpeg")

# 读取图片
image = Image.open(image_path)

# 将图片转换为numpy数组
image_array = np.array(image)

# 翻转图片
flipped_image_array = np.flip(image_array, axis=1)  # 水平翻转

# 创建三张原始图片的拼接
row1 = image_array
row2 = image_array
row3 = image_array

# 按行拼接三张图片
concatenated_image_array = np.vstack((row1, row2, row3))

# 将numpy数组转换回PIL图像
concatenated_image = Image.fromarray(concatenated_image_array)

# 显示原始图片、翻转后的图片和拼接后的图片
plt.figure(figsize=(15, 10))

plt.subplot(1, 3, 1)
plt.title('Original Image')
plt.imshow(image)
plt.axis('off')

plt.subplot(1, 3, 2)
plt.title('Flipped Image')
plt.imshow(Image.fromarray(flipped_image_array))
plt.axis('off')

plt.subplot(1, 3, 3)
plt.title('Concatenated Image')
plt.imshow(concatenated_image)
plt.axis('off')

plt.show()
