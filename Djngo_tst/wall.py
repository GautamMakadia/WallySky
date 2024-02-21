from mysql.connector.connection import *
from config import (execute_query)
from mysql.connector.cursor_cext import CMySQLCursorDict

def get_wallpaper_by_id(conn: MySQLConnection, id: int):
    responce = {}
    status_code = 204

    cursor = execute_query(conn, "select * from wallpaper where id = %s", (id, ), dictionary=True)

    if isinstance(cursor, CMySQLCursorDict):
        result = cursor.fetchall()
        c_count = cursor.rowcount

        if c_count > 0:
            for data in result:
                data['date_added'] = (data["date_added"])
                print(data)
                responce = data
            status_code = 200
        else:
            responce = {
                "msg": "no user found",
                "user_id": id
            }
    elif isinstance(cursor, Exception):
        responce = {
            "exception": f"{cursor.args}"
        }
        status_code = 500

    return responce, status_code 


def get_randome_wallpaper(conn: MySQLConnection):
    responce = {}
    status_code: int = 204

    
    cursor = execute_query(conn, "SELECT * FROM wallpaper ORDER BY RAND()", dictionary=True)

    if isinstance(cursor, CMySQLCursorDict):
        print(f"row count > {cursor.rowcount}")
        result = cursor.fetchall()
        c_count = cursor.rowcount
    
        if c_count > -1:
            for wall in result:
                catagory:str = wall['category']
                wall['category'] = catagory.split(", ") 
                responce[f"'{wall['id']}'"] = wall
            
            status_code = 201
        else:
            responce = {
                "msg": "there is no data found.",
                "status": "204",
            } 
            status_code = 204
            
    elif isinstance(cursor, Exception):
        responce ={"err": cursor.args}
        status_code = 501
        

    return responce, status_code


def get_wallapaper_by_count(conn: MySQLConnection, count: int):
    responce = {}
    status_code = 204

    cursor = execute_query(conn, f"select * from wallpaper limit {count}", dictionary=True)
    
    print(type(cursor))

    if isinstance(cursor, CMySQLCursorDict):
        result = cursor.fetchall()
        c_count = cursor.rowcount

        if c_count > 0:
            print(f"row_count > {c_count}")
            for data in result:
                responce[f"{data['id']}"] = data
            status_code = 201
        else:
            responce = {
                "msg": "there is no data found.",
                "status": "204",
            } 
            status_code = 204

    elif isinstance(cursor, Exception):
        responce = {
            "msg": f"{cursor.args}",
            "status": "500"
        }
        status_code = 500

    return responce, status_code

def get_wallpaper_by_catagory(conn: MySQLConnection, cat: str):
    responce = {}
    status_code = 204
    cursor = execute_query(
        conn, 
        f"select * from wallpaper where category LIKE '%{cat}%'", 
        dictionary=True
    )
   

    if isinstance(cursor, CMySQLCursorDict):
        result = cursor.fetchall()
        count = cursor.rowcount
        print(f"cursor count > {count}")
        if count > 0:
            for data in result:
                responce[f"{data['id']}"] = data
        
            status_code = 201

        else:
            responce = {
                "msg": "there is no data found having this category.",
                "category": f"{cat}",
                "status": "204",
            }
            status_code = 204

    elif isinstance(cursor, Exception):
        responce = {
            "msg": f"{cursor.args}",
            "status": "500"
        }
        status_code = 500

    return responce, status_code