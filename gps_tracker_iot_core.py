import io
import datetime
import pynmea2
import serial
import time
import paho.mqtt.client as mqtt
import ssl
import json
import _thread

def on_connect(client, userdata, flags, rc):
    print("Connected to AWS IoT: " + str(rc))

client = mqtt.Client()
client.on_connect = on_connect
client.tls_set(ca_certs='./rootCA.pem', certfile='./certificate.pem.crt', keyfile='./private.pem.key', tls_version=ssl.PROTOCOL_SSLv23)
client.tls_insecure_set(True)
client.connect("Your_AWS_IoT_Core_EndPoint", 8883, 60)


ser = serial.Serial('/dev/ttyAMA0', 9600, timeout=5.0)
sio = io.TextIOWrapper(io.BufferedRWPair(ser, ser))
msg_no = 1

while 1:
    try:
        line = sio.readline()
        msg = pynmea2.parse(line)
        #print(repr(msg))
        #print (msg)
        newdata = repr(msg)

        if 'RMC' in newdata:
             #raw data lat & lon
            lat = repr(msg.lat)
            lon = repr(msg.lon)
            speed = repr(msg.spd_over_grnd)
            lat_dir = repr(msg.lat_dir)
            lon_dir = repr(msg.lon_dir)
            # remove quotes from lat, lon, lat direction & lon direction
            lat = lat.strip("\'")
            lon = lon.strip("\'")
    
            if lat == "":
                lat = "NA"
                lon = "NA"
                speed = "NA"
            else:

                # converting lat & lon to decimal number
                if float(lat[2:]) == 0:
                    lat = float(lat[:2])
                else:
                    lat = float(lat[:2])+float(lat[2:])/60
                if float(lon[3:]) == 0:
                    lon = float(lon[:3])
                else:
                    lon = float(lon[:3])+float(lon[3:])/60
                # add direction to lat & lon
                if lat_dir == N:
                    lat_dir_sign = 1
                else:
                    lat_dir_sign = -1
                if lon_dir == E:
                    lon_dir_sign = 1
                else:
                    lon_dir_sign = -1
                # final lat & lon values
                lat = lat * lat_dir_sign
                lon = lon * lon_dir_sign 
                
            gpsdata = {"lat" : lat, "lon": lon, "speed": speed, "msg_no": msg_no}
            timestamp_1 = datetime.datetime.now().strftime("%Y/%m/%d, %H:%M:%S")
            print(repr(msg.timestamp))
            gpsdata = {"lat" : lat, "lon": lon, "speed": speed, "msg_no": msg_no, "timestamp_1": timestamp_1}
            print (json.dumps(gpsdata))
            client.publish("raspi/data", payload=json.dumps(gpsdata), qos=0, retain=False)
            msg_no = msg_no + 1
            time.sleep(60)
    except UnicodeDecodeError as e:
        print('Unicode Error')
    except serial.SerialException as e:
        print('Device error: {}'.format(e))
        break
    except pynmea2.ParseError as e:
        print('Parse error: {}'.format(e))
        continue