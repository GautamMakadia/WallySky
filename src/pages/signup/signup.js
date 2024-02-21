import { api_site } from "../../config/site.conf.js";
import { getById } from "../../scripts/comman.js";
import { getSession, setSession } from "../../scripts/session.js";

// import '../../style/pages/signup.scss'
import '../../style/compenent/form.scss'
import '../../style/fonts.scss'


window.onload = async () => {
    document.body.classList.remove('disabled')
    // let res = await fetch('http://localhost:81/auth/')
    // console.log(await res.json());
    let user = await getSession()
    
    if (user != null) {
        // console.log('user : '+ user)
        location.href = '/index.html'
    }

    const form = getById('signup-form')

    form.onsubmit = doSignup

}

const doSignup = async (event) => {
    event.preventDefault();
    console.log('click');

    const form = event.currentTarget
    const submit_btn = document.querySelector('input[name=signup]')
    const form_data = new FormData(form, submit_btn);
    console.log(Array.from(form_data))


    const form_value = Array.from(form_data)

    if (form_value.password != form_value.conf_password) {
        alert("Passwords Dosen't Macth, Please Make Sure Both Are Same.")
        return
    }

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
        alert('Error : '+ error.msg + '\n try again later');
        return
    }
    
    if (login_responce.status == 422) {
        const responce = await login_responce.json()

        alert(`Status: ${responce.status} \nMessage: ${responce.message}`);
        return
    }

    if (login_responce.status == 201) {
        alert('SignUp Successfull.')
        location.href = './login.html'
        return
    }

}