import re

# 正则表达式模式
pattern = r'^www\.[a-zA-Z0-9-]+\.edu\.cn$'

# 网站地址列表
websites = [
    "www.wust.edu.cn",  # 武汉科技大学
    "www.whu.edu.cn",   # 武汉大学
    "www.baidu.com",    # 百度
    "www.hubei.gov.cn", # 湖北省政府
    "www.example.edu.cn", # 示例大学
    "www.invalid-edu.cn" # 无效的大学地址
]

# 使用 for 循环遍历列表并匹配
for website in websites:
    if re.match(pattern, website):
        print(f"匹配成功: {website}")
    else:
        print(f"匹配失败: {website}")