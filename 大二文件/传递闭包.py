def warshall(matrix):
    n = len(matrix)  # 获取行数
    closure = [row[:] for row in matrix]  # 复制矩阵

    # 逐步构建传递闭包
    for k in range(n):#中间节点
        for i in range(n):#行
            for j in range(n):#列
                closure[i][j] = closure[i][j] or (closure[i][k] and closure[k][j])

    return closure


# 获取用户输入的矩阵大小
rows = int(input("请输入矩阵的行数: "))
cols = int(input("请输入矩阵的列数: "))

# 初始化一个空的二维列表
matrix = []

# 获取用户输入的矩阵元素
print("请输入矩阵的元素:")
for i in range(rows):
    while True:
        row_input = input(f"请输入第 {i+1} 行的元素，用空格分隔: ")
        try:
            row = list(map(int, row_input.split()))
            if len(row) != cols:
                print(f"输入的元素数量不正确，应为 {cols} 个。")
                continue
            matrix.append(row)
            break
        except ValueError:
            print("输入的元素无效，请输入整数。")

# 计算传递闭包
closure = warshall(matrix)

# 输出传递闭包矩阵
print("传递闭包矩阵为:")
for row in closure:
    print(row)