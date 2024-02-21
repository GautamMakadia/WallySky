from mysql.connector.connection import (MySQLConnection)
from typing import (Optional, Literal)
from mysql.connector import MySQLConnection
from mysql.connector.types import (ParamsSequenceOrDictType)  
from mysql.connector.cursor_cext import CMySQLCursorDict
from mysql.connector.cursor import MySQLCursor
import mysql.connector

def get_conn() -> MySQLConnection | Literal[-1]:
    try:
        conn = mysql.connector.connect(
            host='localhost',
            user='root',
            password='',
            database='wallysky'
        )
        return conn
    except Exception as err: 
        return -1
    

def execute_query(
    conn: MySQLConnection, 
    query:str, 
    params: Optional[ParamsSequenceOrDictType] = None,
    named_tuple = False, 
    dictionary = False
) -> CMySQLCursorDict | Exception :
    
    try: 
        cursor = conn.cursor(named_tuple=named_tuple, dictionary=dictionary)
        cursor.execute(query, params=params)

        return cursor
    except Exception as err:
        print(query, params)
        print(err)
        return err