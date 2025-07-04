import matplotlib.pyplot as plt
x=[10,15,20,25,30,35,45]
y=[833.2,831.4,829.2,827.4,826.1,825.4,825.3]
plt.plot(x,y,marker='h')
plt.xlabel("x/mm");plt.ylabel("f/Hz")
plt.title("Dynamic method for measuring the Young's modulus of metals")
plt.show()