from flask import (Flask, jsonify, json, request)
from config import get_conn
from wall import *
from auth import *
from user import *
from typing import *
from werkzeug import *
from flask_cors import CORS

app = Flask(__name__)
CORS(app) 



@app.route('/')
def index():
    conn = get_conn()
    args = request.args
    responce = []


    if 'id' in args:
        print(f"request => /?id={args['id']}, get wallpaper by id.")
        responce = get_wallpaper_by_id(conn, args['id'])
    
    # fetch wallpaper record in randome order
    elif 'rand' in args: 
        print(f"request => /?rand, get_wallpaper_randomely")
        responce = get_randome_wallpaper(conn)
    
    # fetch wallpaper records having given catagory
    elif 'cat' in args:
        print(f"request => /?cat={args['cat']}")
        responce = get_wallpaper_by_catagory(conn, args['cat'])
    
    elif 'count' in args:
        print(f"request => /?count={args['count']}")
        responce = get_wallapaper_by_count(conn, args['count'])

    elif 'uid' and 'fav' in args:
        print(f"request => /?fav&id={args['uid']}")
        responce = get_user_fav_walls(conn, args['uid'])
    
    elif len(args) == 0: return "<p>Wallysky<p>"

    conn.commit()
    conn.close()

    return jsonify(responce[0]), responce[1]



@app.route('/auth/', methods=['POST'])
def authentication():
    conn = get_conn()
    responce = []
    
    args = request.form
    if 'login' in args:
        responce = do_login(conn, args['email'], args['password'])
    
    elif 'signup' in args:
        responce = do_sign_up(conn, args['username'], args['email'], args['password'])

    elif len(args) == 0 :  return "<p>Wallysky, Authentication Api.<p>"

    conn.commit()
    conn.close()
    return jsonify(responce[0]), responce[1] 


@app.route('/user/', methods=['POST', 'GET'])
def user_route():
    responce = []
    conn = get_conn()
    
    method = request.method

    if method == 'GET':
        args = request.args
        if 'id' in args:
            responce = get_user_detail(conn, args['id'])
        elif 'paid' and 'uid' in args:
            responce = get_paid_wall(conn, args['uid'])


    if method == 'POST':
        args = request.form

        if 'fav' in args:
            responce = toggle_fav_wall(conn, args['wall_id'], args['uid'])
        
    conn.commit()
    conn.close()
    return jsonify(responce[0]), responce[1]





if __name__ == "__main__":
    json.provider.DefaultJSONProvider.sort_keys=False
    app.run(debug=True)