from mysql.connector.connection import MySQLConnection
from mysql.connector.cursor_cext import CMySQLCursorDict
from mysql.connector.cursor import CursorBase, MySQLCursor
from config import (execute_query)

def get_user_detail(conn: MySQLConnection, id: int):
    responce = {}
    status_code = 204

    cursor = execute_query(conn, "select * from users where id = %s", (id, ), dictionary=True)
    
    if isinstance(cursor, CMySQLCursorDict):
        result = cursor.fetchall()
        if 0 < cursor.rowcount:
            responce = result.pop()
            status_code = 200
    elif isinstance(cursor, Exception):
        responce = {
            "msg": f"{cursor.args}"
        }
        status_code = 500

    return responce, status_code


def get_user_fav_walls(conn: MySQLConnection, id: int):
    responce = {}
    status_code = 204

    cursor = execute_query(conn, "select * from favorite where user_id = %s", (id, ), dictionary=True)
    
    if isinstance(cursor, CMySQLCursorDict):
        result = cursor.fetchall()
        print(cursor.rowcount)
        if cursor.rowcount > 0:
            for data in result:
                responce[data['wall_id']] = data
            status_code = 200
    
    if isinstance(cursor, Exception):
        responce = {
            "status": "500",
            "msg": "mysql_error",
            "error": f"{cursor.args}"
        } 

        status_code = 500

    return responce, status_code

def get_paid_wall(conn: MySQLConnection, uid: int):
    responce = {}
    status_code = 204

    cursor = execute_query(conn, "select * from payment where user_id = %s", (uid, ), dictionary=True)

    if isinstance(cursor, CMySQLCursorDict):
        result = cursor.fetchall()

        if 0 < cursor.rowcount:
            for data in result:
                responce[data['wall_id']] = data
            status_code = 200

    elif isinstance(cursor, Exception):
        responce = {
            "status": "500",
            "msg": "mysql_error",
            "error": f"{cursor.args}"
        } 

        status_code = 500

    return responce, status_code


def toggle_fav_wall(conn:MySQLConnection, wall_id: int, uid: int):
    responce = []
    status_code = 204
    query = f"select id from favorite where user_id={uid} and wall_id={wall_id}"

    cursor = conn.cursor(buffered=True, named_tuple=True)
    update_cursor = conn.cursor(buffered=True, named_tuple=True)
    cursor.execute(query)


    print(f"row count: {cursor.rowcount}")

    try:
        if cursor.rowcount == 0:
            print("insert into fav.")
            update_query = f"insert into favorite (wall_id, user_id) values({wall_id}, {uid})"
            print(update_query)
            update_cursor.execute(update_query)

            status_code = 201
            responce = {
                "id": f"{update_cursor.lastrowid}",
                "wall_id": f"{wall_id}",
                "user_id": f"{uid}",
                "msg": "toggle on",
                "isFav": True
            }


        elif cursor.rowcount == 1:
            print("delete from fav.")

            row = cursor.fetchone()
            print(f"delete id: {row.id}")
            update_query = f"delete from favorite where `id`= {row.id}"
            print(update_query)
            update_cursor.execute(update_query)
                
            status_code = 201
            responce = {
                "id": f"{update_cursor.lastrowid}",
                "wall_id": f"{wall_id}",
                "user_id": f"{uid}",
                "msg": "toggle off",
                "isFav": False,
            }

    except Exception as err:
        status_code = 204
        responce = {
            "msg": f"{err.args}"
        }


    cursor.close()
    update_cursor.close()

    return responce, status_code