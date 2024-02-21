import { api_site } from "../../config/site.conf.js";
import { getById } from "../../scripts/comman.js";
import { getSession, setSession } from "../../scripts/session.js";
import '../../style/compenent/form.scss'
import '../../style/fonts.scss'


window.onload = async () => {
    document.body.classList.remove('disabled')
    
    let user = await getSession()
    
    if (user != null) {
        // console.log('user : '+ user)
        location.href = '/index.html'
    }

    const form = getById('login-form')

    form.onsubmit = doLogin
}

const doLogin = async (event) => {
    event.preventDefault();
    console.log('click');

    const form = event.currentTarget
    const submit_btn = document.querySelector('input[name=login]')
    const form_data = new FormData(form, submit_btn);

    // console.log(Array.from(form_data))

    let login_responce = null
    let responce = null

    try {
        login_responce = await fetch(api_site+'auth/', {
            method: 'POST',
            body: form_data
        })

        if (!login_responce.ok) {
            if (login_responce.status === 500) {
                console.log('sql error.')
                throw await login_responce.json()
            }
        }

    } catch(error) {
        console.log(error);
        alert('Error : '+ error + '\n try again later');
        return;
    }
    
    if (login_responce.status == 204) {
        alert('password or email provided are wrong, please try again.');
    }

    if (login_responce.status == 200) {
        responce = await login_responce.json()
        console.log(responce);

        if (await setSession(responce.user.id)) {
            alert('Login Successfull.')
            location.href = './index.html'
        } else {
            alert('messedup.')
        }    
    }

}