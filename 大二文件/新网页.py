from pywebio.output import *
from pywebio import start_server
from pywebio.input import *

# 定义显示不同图表的函数
def zhexian():
    put_image(open("折线图.png", 'rb').read())

def zhu():
    put_image(open("柱状图.png", 'rb').read())

def san():
    put_image(open("饼状图.png", 'rb').read())

def gui():
    put_image(open("轨迹图.png", 'rb').read())

def main():
    # 显示菜单
    put_html("<h1>--------交通分析----------</h1>")
    put_html("<hr>")
    put_html("<h2>请选择要查看的图表类型：</h2>")
    chart_type = select("选择图表类型", ["饼状图", "柱状图", "折线图", "轨迹图"])
    # 创建并显示选定的图表
    if chart_type == "饼状图":
        san()
        put_button(label='返回', onclick=main)
    elif chart_type == "柱状图":
        zhu()
        put_button(label='返回', onclick=main)
    elif chart_type == "折线图":
        zhexian()
        put_button(label='返回', onclick=main)
    elif chart_type == "轨迹图":
        gui()
        put_button(label='返回', onclick=main)

# 启动服务器
start_server(main, port=5050)