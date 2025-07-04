import re
a="Hello"
b="~1#World"
pattern="~1#"
match = re.match(pattern, b)
if match:
    a+=b
    print(a)
    print("biubiubiu")
# 生成1到100之间的所有数字
numbers = list(range(1, 101))

# 正则表达式模式，匹配包含数字2的数字
pattern = r'.*2.*'

# 使用列表推导式和正则表达式匹配包含数字2的数字
numbers_with_2 = [num for num in numbers if re.match(pattern, str(num))]

# 输出结果
print(numbers_with_2)