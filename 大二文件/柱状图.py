import pandas as pd
from pyecharts.charts import Bar
from pyecharts import options as opts

# 定义一个柱状图对象
bar = Bar()
# 读取csv
path = "C:\\Users\\HUAWEI\\Desktop\\附件\\D.csv"
data = pd.read_csv(path,nrows=1000)

# 按照vehicle_id进行分组
grouped = data.groupby('vehicle_id')

# 对每个分组进行等比例抽样，例如每个分组抽取10%的样本
sampled = grouped.sample(frac=0.1)

# 计算抽样后的平均速度
sampled_result_x = sampled.groupby('vehicle_id')['x'].mean()
sampled_result_y = sampled.groupby('vehicle_id')['y'].mean()
sampled_result = (sampled_result_x**2 + sampled_result_y**2)**0.5

# 将结果转换为DataFrame
sampled_result_df = pd.DataFrame(sampled_result).reset_index()
sampled_result_df.columns = ['id', 'v']


# 将平均速度转换为列表
v_list = result['v'].tolist()

# 设置横轴的数据项（vehicle_id）
bar.add_xaxis(result['id'].tolist())

# 设置纵轴的数据项和对应值（平均速度）
bar.add_yaxis("车辆通过路口平均速度", v_list)

# 设置图表的标题和坐标轴的标签
bar.set_global_opts(
    title_opts={"text": "车辆通过路口平均速度"},
    xaxis_opts={
        "name": "车辆ID",
        "name_location": "middle",  # x轴名称位置
        "name_gap": 25,             # x轴名称与x轴之间的距离
        "axislabel_opts": {"rotate": 45}  # 旋转横轴标签
    },
    yaxis_opts={"name": "平均速度"}
)

# render 会生成本地 HTML 文件，默认会在当前目录生成 render.html 文件
# 也可以传入路径参数，如 bar.render("mycharts.html")
bar.render("101.html");print("成功")

