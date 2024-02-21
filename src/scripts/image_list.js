import { api_site, image_site } from "../config/site.conf.js";
import { MDCIconButtonToggle } from '@material/icon-button';
import { MDCDialog } from '@material/dialog';
import { getById } from "./comman.js";
import { site_state } from "../index.js";


export const setMasonryImageItem = (image, list) => {

    const id = image.id
    const url = image.url
    let isFav = false
    const isPrem = image.is_premium > 0
    let isPaid = false

    const down_lnk = encodeURI(`${image_site}/api/?down=${url}`)
    console.log({
        id: image.id,
        title: image.title,
        down_link: down_lnk,
        isFav: isFav,
        isPrem: isPrem,
        isPaid:isPaid,
        category: image.category
    })

    if (Object.keys(site_state.fav_list).length != 0) {
        isFav = Object.keys(site_state.fav_list).includes(`${id}`)
    }

    if (Object.keys(site_state.paid_walls).length != 0) {
        isPaid = Object.keys(site_state.paid_walls).includes(`${id}`)
    }


    console.log({ isAlreadyPaid: isPaid, wall_id: id })
    const li = document.createElement('li')
    const imageCard = document.createElement('div')
    const img = document.createElement('img')

    const info_div = document.createElement('div')
    const info_div_left = document.createElement('div')
    const info_div_right = document.createElement('div')
    const fav_button = document.createElement('button')
    const icon_ripple = document.createElement('div')

    const titel = document.createElement('span')
    const artist = document.createElement('span')


    if (site_state.user_id != null) {
        fav_button.id = `fav-btn-${id}`
        icon_ripple.classList.add("mdc-icon-button__ripple")
        fav_button.classList.add(
            'mdc-icon-button',
            'mdc-card__action',
            'mdc-card__action--button',
            'material-symbols-rounded',
            'fav-btn'
        )

        info_div_right.append(fav_button)

        fav_button.textContent = 'favorite'
        fav_button.append(icon_ripple)

        fav_button.onclick = async (event) => {
            event.preventDefault()
            event.stopImmediatePropagation()
            isFav = await handleFavClick(image, li, event)
        }

        li.classList.toggle('wall-fav-item', isFav)
        const fav_toggle = new MDCIconButtonToggle(fav_button)
        fav_toggle.on = isFav
        fav_toggle.destroy()
    }


    const max_dialog = new MDCDialog(document.querySelector('#max-wall-dialog'))

    imageCard.onclick = (event) => {
        event.stopPropagation()
        onDialogOpening(image)
        max_dialog.open()
    }

    li.id = `wall-list-item-${id}`
    imageCard.id = `card-${id}`
    img.id = `card-img-${id}`
    titel.id = `card-title-${id}`
    artist.id = `card-artist-${id}`


    li.classList.add('mdc-image-list__item', 'wall-list-item')
    imageCard.classList.add('mdc-card', 'mdc-card--outlined', 'image-card')
    img.classList.add("mdc-image-list__image", "card-img")

    image.category.forEach(element => {
        li.classList.add(element)
        imageCard.classList.add(element)
        img.classList.add(element)
    });

    info_div.classList.add("card-info")
    info_div_left.classList.add('card-info-left')
    info_div_right.classList.add('card-info-right')

    titel.classList.add('card-title')
    artist.classList.add('card-artist')

    titel.textContent = image.title
    artist.textContent = 'by ' + image.artist
    img.src = image_site + 'compressed/' + image.url
    img.loading = "lazy"


    info_div_left.append(titel, artist)
    info_div.append(info_div_left, info_div_right)
    imageCard.append(img, info_div)
    li.append(imageCard)
    list.append(li)


    max_dialog.listen('MDCDialog:closed', (event) => {
        const wall_img = getById('dialog-wall-img')
        const wall_title = getById('dialog-wall-title')
        const wall_artist = getById('dialog-wall-artist')
        const payment_content = getById('payment-content')
        const wall_fav_toggle = getById('dialog-fav-btn')
        const wall_down_btn = getById('dialog-down-btn')
        const payment_btn = getById('dialog-payment-btn')

        wall_img.src = "#"
        wall_title.textContent = ""
        wall_artist.textContent = ""

        wall_fav_toggle.classList.toggle('mdc-icon-button--on', false)
        payment_content.classList.add('disabled')
        payment_btn.classList.add('disabled')
        wall_down_btn.classList.add('disabled')

        wall_fav_toggle.removeEventListener('click', dialog_favaction)
        wall_down_btn.removeEventListener('click', dialog_down_action)
    })

    function onDialogOpening(image) {
        const wall_img = getById('dialog-wall-img')
        const wall_title = getById('dialog-wall-title')
        const wall_artist = getById('dialog-wall-artist')
        const payment_btn = getById('dialog-payment-btn')
        const wall_fav_toggle = getById('dialog-fav-btn')
        const wall_down_btn = getById('dialog-down-btn')

        wall_img.src = image_site + image.url
        wall_title.textContent = image.title
        wall_artist.textContent = `by ${image.artist}`

        if (site_state.user_id != null) {
            payment_btn.classList.toggle('disabled', !isPrem)
            payment_btn.get
            payment_btn.onclick = dialog_payment_action
        } else {
            payment_btn.onclick = (event) => {
                event.stopPropagation()
                event.preventDefault()
                alert('Please Login Fisrt To Make Any Payment.')
                location.href = 'login.html'
            }
        }


        if (isPrem) {

            if (site_state.user_id == image.artist_id) {
                wall_down_btn.classList.toggle('disabled', false)
                payment_btn.classList.toggle('disabled', true)
            } else {
                wall_down_btn.classList.toggle('disabled', !isPaid)
                payment_btn.classList.toggle('disabled', isPaid)
            }

        } else {
            console.log('show down')
            wall_down_btn.classList.remove('disabled');
            payment_btn.classList.add('disabled');
        }


        wall_fav_toggle.classList.toggle('mdc-icon-button--on', isFav)

        wall_fav_toggle.onclick = dialog_favaction
        wall_down_btn.onclick = dialog_down_action
    }

    async function dialog_favaction(event) {
        event.preventDefault()
        event.stopImmediatePropagation()
        const fav_toggle = new MDCIconButtonToggle(fav_button)
        isFav = await handleFavClick(image, li, event)
        fav_toggle.on = isFav
        fav_toggle.destroy()
        event.currentTarget.blur()
    }

    async function dialog_down_action(event) {
        event.preventDefault()
        event.stopPropagation()

        const apiURL = down_lnk;

        // Create a link element to initiate the download
        const link = document.createElement('a');
        link.href = apiURL;

        // Replace 'new_filename.jpg' with the desired download filename
        link.download = image.title + "." + image.mime_type;

        // Programmatically click the link to trigger the download
        link.click();

        event.currentTarget.blur()
    }

    async function dialog_payment_action(event) {
        event.preventDefault()
        event.stopPropagation()
        getById('price').textContent = "Price : " + image.price
        
        getById('payment-form').onsubmit = doPayment
        getById('payment-content').classList.remove('disabled')

        event.currentTarget.blur()
    }

    async function doPayment(event) {
        event.preventDefault()
        const form = event.currentTarget
        const formData = new FormData(form, getById('payment'))
        formData.set('wall_id', image.id)
        formData.set('uid', site_state.user_id)
        formData.set('price', image.price)

        const pay_res = await fetch(api_site + 'user/', {
            method: 'POST',
            body: formData
        })

        
        if (pay_res.status == 500) {
            const res = await pay_res.json()
            alert(res.error_msg);
            return
        } else if (pay_res.status == 201) {
            alert('Payment Successfull, Now You Can Download.')
            getById('payment-content').classList.add('disabled')
            getById('dialog-down-btn').classList.remove('disabled')
            getById('dialog-payment-btn').classList.add('disabled')
            isPaid = true
            site_state.paid_walls[image.id] = image
            console.log(site_state.paid_walls)
        } else if (pay_res.status == 200) {
            const res = await pay_res.json()
            alert(res.details)
        } else if (pay_res.status == 204) {
            alert('Password Is Wrong.')
            return
        }


    }

}


async function handleFavClick(image, li, event) {
    const fav_btn = event.currentTarget
    const fav_toggle = new MDCIconButtonToggle(fav_btn)

    const formData = new FormData()
    formData.set('wall_id', image.id)
    formData.set('uid', site_state.user_id)
    formData.set('fav', '')

    const fav_res = await fetch(api_site + 'user/', {
        method: 'POST',
        body: formData
    })

    let state = {}
    if (fav_res.status == 201) {
        state = await fav_res.json()
        console.log({ state: state, uid: site_state.user_id, wall_id: image.id })

        if (state.isFav) {
            site_state.fav_list[image.id] = {
                id: state.id,
                uid: site_state.user_id,
                wall_id: image.id
            }
        } else {
            delete site_state.fav_list[image.id]
        }


        li.classList.toggle('wall-fav-item', state.isFav)
        fav_toggle.on = state.isFav

    } else {
        fav_toggle.destroy()
        return false
    }

    if (fav_res.status == 500) {
        console.log(await fav_res.json())
    }

    fav_btn.blur();
    fav_toggle.destroy()
    return state.isFav
}



const setStanderdImageItem = (image, list) => {
    const li = document.createElement('li')
    const img = document.createElement('img')
    const img_cont = document.createElement('div')

    img.id = `img=${image.id}`
    img.src = image.url

    img_cont.classList.add('mdc-image-list__image-aspect-container')
    li.classList.add('mdc-image-list__item', 'wall-item')
    img.classList.add("mdc-image-list__image", "wall-img")

    li.append(img_cont)
    img_cont.append(img)
    list.append(li)
}

