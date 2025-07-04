import numpy as np
import pandas as pd
from sklearn.linear_model import LogisticRegression #逻辑回归   
from sklearn.model_selection import GridSearchCV, train_test_split #网格搜索
from sklearn.preprocessing import StandardScaler #标准差标准化
from sklearn.metrics import accuracy_score #准确率
 
# 示例数据（鸢尾花数据集）
from sklearn.datasets import load_iris
data = load_iris()
X, y = data.data, data.target

# 数据预处理
X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.2, random_state=42)
scaler = StandardScaler()
X_train = scaler.fit_transform(X_train)
X_test = scaler.transform(X_test)

# 定义模型和参数网格，自动调整正则项参数，实现风险函数最小化
model = LogisticRegression(max_iter=1000)
param_grid = {
    'C': [0.001, 0.01, 0.1, 1, 10, 100],  # 正则化强度的倒数
    'penalty': ['l1', 'l2'],               # 正则化类型
    'solver': ['liblinear']                # 适用于L1/L2的求解器
}

# 网格搜索优化
grid_search = GridSearchCV(model, param_grid, cv=5, scoring='accuracy')
grid_search.fit(X_train, y_train)

# 输出最优参数和模型性能
print("Best Parameters:", grid_search.best_params_)
best_model = grid_search.best_estimator_
y_pred = best_model.predict(X_test)
print("Test Accuracy:", accuracy_score(y_test, y_pred))