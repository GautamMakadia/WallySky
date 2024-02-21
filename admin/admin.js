async function approve(id) {
    const approve_res = await fetch('http://localhost:3000/api/?approve='+id)

    if (approve_res.status == 500) {
        const res = await approve_res.json()
        alert(res)
        return
    }
    
    if (approve_res.status == 204){
        alert('Somthing Went Wrong!')
        return
    }
    
    if (approve_res.status == 201) {
        const res = await approve_res.json()
        alert(res.msg)
        window.location.reload()
        return
    } 
}


async function decline(id) {
    const approve_res = await fetch('http://localhost:3000/api/?decline='+id)

    if (approve_res.status == 500) {
        const res = await approve_res.json()
        alert(res)
        return
    }
    
    if (approve_res.status == 204){
        alert('Somthing Went Wrong!')
        return
    }
    
    if (approve_res.status == 201) {
        const res = await approve_res.json()
        alert(res.msg)
        window.location.reload()
        return
    } 
}