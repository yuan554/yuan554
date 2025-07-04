import re
name = "  Alex"
# a. 移除name变量对应的值两边的空格并输出移除后的内容
name_stripped = re.sub(r'^\s+|\s+$', '', name)
print("a. 移除空格后的内容:", name_stripped)

# b. 判断 name 变量对应的值是否以 "Al" 开头并输出结果
starts_with_Al = bool(re.match(r'^Al', name_stripped))
print("b. 是否以 'Al' 开头:", starts_with_Al)

# c. 判断 name 变量对应的值是否以 "X" 结尾并输出结果
ends_with_X = bool(re.search(r'X$', name_stripped))
print("c. 是否以 'X' 结尾:", ends_with_X)

# d. 将 name 变量对应的值中的 “l” 替换为“c”并输出结果
name_replaced = re.sub(r'l', 'c', name_stripped)
print("d. 替换后的内容:", name_replaced)

# e. 将 name 变量对应的值根据 “l”分割并输出结果
name_split = re.split(r'l', name_stripped)
print("e. 分割后的内容:", name_split)

# f. 将 name 变量对应的值变为大写并输出结果
name_upper = name_stripped.upper()
print("f. 大写后的内容:", name_upper)