from mysql.connector.connection import MySQLConnection
from mysql.connector.cursor_cext import CMySQLCursorDict
from config import (execute_query)
from flask import (jsonify)
import hashlib

def do_login(conn: MySQLConnection, user_id: str, password: str):
    responce = {}
    status_code = 204
    _pass = hashlib.md5(password.encode()).hexdigest()
    user_crsr = f"select id, username, email from users where email = '{user_id}' and `password`= '{_pass}'"

    cursor = execute_query(conn, user_crsr, dictionary=True)

    
    if isinstance(cursor, CMySQLCursorDict):
        result = cursor.fetchall() 
        print(result)
        print(cursor.rowcount)   
        if cursor.rowcount > 0:  
            for data in result:
                responce = {
                    "user": data
                }
            status_code = 200

    elif isinstance(cursor, Exception):
        responce = {
            "exception": f"{cursor.args}"
        }
        status_code = 500
            
    return responce, status_code


def do_sign_up(conn: MySQLConnection, username: str, email: str, password: str):
    responce = {"default":"default"}
    status_code = 422
    password = hashlib.md5(password.encode()).hexdigest()

    user_crsr = execute_query(conn, "select id from users where username=%s", (username, ), dictionary=True)
    user_crsr.fetchall()
    email_crsr = execute_query(conn, "select id from users where email=%s", (email, ), dictionary=True)
    email_crsr.fetchall()

    if not isinstance(user_crsr, Exception) or not isinstance(email_crsr, Exception):
        user_count = user_crsr.rowcount
        email_count = email_crsr.rowcount

        if user_count > 0 and email_count > 0:
            responce = {
                "status": 422,
                "message": "User & Email Alredy Exist, Please Try Another",
                "email": email,
                "username": username
            }
        elif user_count > 0:
            responce = {
                "status": 422,
                "message": "User Alredy Exist, Please Try Another",
                "email": email,
                "username": username
            }
        elif email_count > 0:
            responce = {
                "status": 422,
                "message": "Email Alredy Exist, Please Try Another",
                "email": email,
                "username": username
            }
        else:
            cur = execute_query(conn, "insert into users(username, email, password) value(%s, %s, %s)", (username, email, password, ))
            if isinstance(cur, Exception):
                responce = {
                    "mysql": f"{cur.args}"
                }
                status_code = 500
            elif cur.lastrowid != None:
                status_code = 201
                
    elif isinstance(user_crsr, Exception) or isinstance(email_crsr, Exception):
        responce = {
            "exception": f"{email_crsr.args}"
        }
        status_code = 500

    return responce, status_code


