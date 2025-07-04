import pandas as pd
from textblob import TextBlob
import matplotlib.pyplot as plt
import numpy as np

# 读取数据
df = pd.read_csv("C:\\Users\\yuan\\Desktop\\lecture 6\\热水器评价.csv")

# 进行情感分析
df['sentiment'] = df['review'].apply(lambda x: TextBlob(x).sentiment.polarity)

# 分段统计情感分数
bins = np.linspace(-1, 1, 11)  # 10个分数段，从-1到1
df['sentiment_bin'] = pd.cut(df['sentiment'], bins)
sentiment_counts = df['sentiment_bin'].value_counts().sort_index()

# 绘制柱状图
plt.figure(figsize=(10, 6))
sentiment_counts.plot(kind='bar', color='skyblue')
plt.title('情感分数段出现频率')
plt.xlabel('情感分数段')
plt.ylabel('频率')
plt.xticks(rotation=45)
plt.show()

# 计算均值和中位数
mean_sentiment = df['sentiment'].mean()
median_sentiment = df['sentiment'].median()

print(f"情感分数均值: {mean_sentiment:.2f}")
print(f"情感分数中位数: {median_sentiment:.2f}")

