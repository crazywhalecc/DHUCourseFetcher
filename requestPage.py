import requests as req
import json
import sys
import os

if len(sys.argv) <= 1:
    print("Too few arguments.")
    print("Usage: python requestPage.py <school-id> <jw-password>")
    exit()
if len(sys.argv) == 2:
    md5 = "tmp_md5/" + sys.argv[1] + ".json"
    if not os.path.exists(md5):
        f = open("tmp_classtable/" + sys.argv[1] + ".txt", "w")
        f.write("error")
        f.close()
        exit()
    userdata = open(md5, "r", encoding="utf-8")
    userdata = userdata.read()
    it = json.loads(userdata)

    username = it["username"]
    pwd = it["pwd"]
else:
    username = sys.argv[1]
    pwd = sys.argv[2]
    print("用户名是：" + username + "\n密码是：" + pwd)

try:
    if not os.path.exists("tmp_classtable/"):
        os.mkdir("tmp_classtable/")
    """print(users[item]["sid"],users[item]["id"])"""
    data = {"userName": username, "userPwd": pwd}
    s = req.session()
    res = s.post("http://jwdep.dhu.edu.cn/dhu/login_wz.jsp", data, timeout=10)
    text = str(res.content, "gbk")
    if "用户名和密码输入错误" in text:
        f = open("tmp_classtable/" + sys.argv[1] + ".txt", "w")
        f.write("pwd_error")
        f.close()
    else:
        print("成功登陆！")
        """res = s.get("http://jwdep.dhu.edu.cn/dhu/student/modifyselfinfo.jsp")"""
        """info = str(res.content,"gbk")"""
        """s_class = re.search("<td>班级</td><td>(.*)</td>",info)"""
        """s_class = s_class.groups()[0]"""
        classtable = s.get("http://jwdep.dhu.edu.cn/dhu/student/selectcourse/seeselectedcourse.jsp", timeout=10).content
        classtable = str(classtable, "gbk")
        print("成功获取课表，正在储存...")
        f = open("tmp_classtable/" + sys.argv[1] + ".txt", "w")
        f.write(classtable)
        f.close()
except KeyError:
    f = open("tmp_classtable/" + sys.argv[1] + ".txt", "w")
    f.write("error")
    f.close()
    pass
except req.RequestException:
    print("请求失败！")
    f = open("tmp_classtable/" + sys.argv[1] + ".txt", "w")
    f.write("error")
    f.close()
    pass
except UnicodeDecodeError:
    f = open("tmp_classtable/" + sys.argv[1] + ".txt", "w")
    f.write("error")
    f.close()
    pass
except TimeoutError:
    f = open("tmp_classtable/" + sys.argv[1] + ".txt", "w")
    f.write("error")
    f.close()
except FileNotFoundError:
    print("未监测到文件")
    f = open("tmp_classtable/" + sys.argv[1] + ".txt", "w")
    f.write("error")
    f.close()