
from pywebio.output import *
from pywebio import start_server
from pywebio.input import *

# 主函数，显示菜单和处理用户输入s
def zhexian():
    put_image(open("折线图.png",'rb').read())

def zhu():
    put_image(open("柱状图.png",'rb').read())

def san():
    put_image(open("饼状图.png",'rb').read())

def gui():
    put_image(open("轨迹图.png",'rb').read())

def main():
    # 显示菜单
    put_html("<h1>--------交通分析----------</h1>")
    put_html("<hr>")
    put_html("<h2>请选择要查看的图表类型：</h2>")
    chart_type = select("选择图表类型", ["散点图", "柱状图","折线图","轨迹图"])
    # 创建并显示选定的图表
    if chart_type == "饼状图":
        san==san()       
        put_button(label='返回',onlick=main())
    elif chart_type == "柱状图":
        zhu==zhu()
        put_button(label='返回',onlick=main())
    elif chart_type == "折线图":
        zhexian == zhexian()
        put_button(label='返回',onlick=main())
    elif chart_type == "轨迹图":
        gui ==gui()
        put_button(label='返回',onlick=main())

#启动服务器
start_server(main,port=5050)