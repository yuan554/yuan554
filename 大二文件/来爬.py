from selenium import webdriver
import requests
import time

# 设置Chrome选项
options = webdriver.ChromeOptions()
options.add_argument('--headless')  # 无头模式

# 启动Chrome浏览器
driver = webdriver.Chrome(options=options)

# 打开目标网页
driver.get('https://i0.hdslb.com/bfs/static/jinkela/long/images/favicon.ico')

# 等待页面加载完成
time.sleep(5)

# 查找视频元素
video = driver.find_element_by_tag_name('video')
video_url = video.get_attribute('src')

# 下载视频文件
if video_url:
    video_response = requests.get(video_url)
    with open('video.mp4', 'wb') as f:
        f.write(video_response.content)
    print("视频下载完成")
else:
    print("未找到视频文件")

# 关闭浏览器
driver.quit()