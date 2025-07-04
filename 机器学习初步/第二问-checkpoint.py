import pandas as pd
import matplotlib.pyplot as plt
path="D:\QQ D file\lecture2\sales-funnel.csv"
data=pd.read_csv(path)
df=pd.DataFrame(data)
df.set_index(["Rep","Manager"],inplace=True)
print(df)
# 搜索价格（Price）最高的产品
max_price_product = df.loc[df['Price'].idxmax()]
print("\n价格最高的产品:")
print(max_price_product)

# 搜索数量（Quantity）最低的产品
min_quantity_product = df.loc[df['Quantity'].idxmin()]
print("\n数量最低的产品:")
print(min_quantity_product)

# 计算在这二者分类下 Account 的平均数和极差
max_price_account = df.loc[df['Price'].idxmax(), 'Account']
min_quantity_account = df.loc[df['Quantity'].idxmin(), 'Account']

# 计算 Account 的平均数和极差
account_mean = df['Account'].mean()
account_range = df['Account'].max() - df['Account'].min()

print("\n价格最高的产品对应的 Account:")
print(max_price_account)
print("\n数量最低的产品对应的 Account:")
print(min_quantity_account)
print("\nAccount 的平均数:")
print(account_mean)
print("\nAccount 的极差:")
print(account_range)


# 创建透视表，按 Product 分类汇总商品价格
pivot_table = df.pivot_table(index='Product', values='Price', aggfunc='mean')

# 打印透视表
print("透视表:")
print(pivot_table)

# 绘制条形图
pivot_table.plot(kind='bar', legend=False)
plt.title('Goods Price Under Different Product Series')
plt.xlabel('Product')
plt.ylabel('Price')
plt.show()