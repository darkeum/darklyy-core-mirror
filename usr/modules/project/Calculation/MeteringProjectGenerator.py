from os import replace
import ezdxf
import re
import json
from sys import argv
import random
import uuid

from ezdxf.filemanagement import decode_base64


#получение входных аргументов
try:   
    req = json.loads(argv[1])
    tpl = argv[2]
    dir = argv[3]
except Exception as e:
    exit()
# print('ddddd')
#рандомное имя
name = (uuid.uuid4())
nameStr = str(name)
nameDXF = nameStr + '.dxf'

doc = ezdxf.readfile(tpl)
data = req
def replacesr(match):
    return str(data.get(match.group(1)))

msp = doc.modelspace()

for i in msp:
    if i.dxftype() == "TEXT":
        i.dxf.text = re.sub(r'\${([a-zA-Z0-9_]+)}', replacesr, str(i.dxf.text))
        i.dxf.text = re.sub(r'\$\\{([a-zA-Z0-9_]+)\\}', replacesr, str(i.dxf.text))
    elif (i.dxftype() == "MTEXT"):
        i.text = re.sub(r'\${([a-zA-Z0-9_]+)}', replacesr, str(i.text))
       # i.text = re.sub(r'\$\[([a-zA-Z0-9_]+)\]', replacesr, str(i.text))
        i.text = re.sub(r'\$\\{([a-zA-Z0-9_]+)\\}', replacesr, str(i.text))
    elif (i.dxftype() == "MULTILEADER"):
        i.context.mtext.default_content = re.sub(
            r'\$\\{([a-zA-Z0-9_]+)\\}', replacesr, str(i.context.mtext.default_content))

doc.saveas(dir + nameDXF)

print(dir + nameDXF)
