import { api_site } from "../config/site.conf.js"

export const getSession = async () => {
    const session_res = await fetch('http://localhost/session.php')

    if (session_res.status == 200) {
        console.log('user found')
        const user = await session_res.json()
        console.log('uid : '+ user.uid)
        return user.uid
    }

    return null
}

export const setSession = async (id) => {
    const form_data = new FormData()
    form_data.append('uid', `${id}`)

    const session_res = await fetch('http://localhost/session.php', {
        method: 'POST',
        body: form_data
    })

    
    return session_res.status == 200
}

export const unsetSession = async () => {
    const session_res = await fetch('http://localhost/session.php?unset') 

    if (session_res.status == 202) {
        return true
    }

    return false
}

