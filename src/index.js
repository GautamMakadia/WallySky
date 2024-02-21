import { api_site, image_site } from './config/site.conf.js';

import { setMasonryImageItem } from './scripts/image_list.js';

import { MDCTopAppBar } from "@material/top-app-bar";

import { MDCIconButtonToggle } from '@material/icon-button';

import { MDCMenu } from '@material/menu';

import { MDCChipActionType, MDCChipCssClasses, MDCChipSet } from '@material/chips';

import { MDCDialog } from '@material/dialog'

import { getSession, unsetSession } from './scripts/session.js';

import { getById } from './scripts/comman.js';
// import site_logo from './style/image/cyclone-svgrepo-com.svg';
// import { setUpMaxWallDialog } from './scripts/max-dialog.js';
import './style/pages/index.scss';
import { setUpUploadDialog } from './scripts/upload_dialog.js';


export const site_state = {
    user_id: null,
    user: null,
    fav_list: {},
    image_list: null,
    paid_walls: {},
    active_filter: {}
}


window.onload = async () => {
    document.body.classList.remove('disabled')
    site_state.user_id = await getSession()
    console.log('user: ' + site_state.user_id)

    setUpTopAppBar()
    setUpChipSet()
    // setUpMaxWallDialog()
    setUpMenu()



    if (site_state.user_id != null) {
        site_state.fav_list = await setUpFavList()
        site_state.paid_walls = await setUpPaidWallList()
        // await getUser(site_state.user_id)
    }

    await setUpWallpaperList()

    setUpUploadDialog()
}


const getUser = async (id) => {



}

const setUpTopAppBar = () => {

    const topAppBarElement = document.querySelector('#top-app-bar')
    const topAppBar = new MDCTopAppBar(topAppBarElement)

    const fav_action = getById('nav-fav-action')
    const login_action = getById('nav-login-action')
    // const nav_menu_action = getById('nav-menu-btn')
    const nav_upload_button = getById('nav-upload-action')
    
    // getById('site-logo').src = site_logo

    fav_action.classList.toggle('disabled', site_state.user_id == null)
    nav_upload_button.classList.toggle('disabled', site_state.user_id == null)
    login_action.classList.toggle('disabled', site_state.user_id != null)


    const fav_iconToggle = new MDCIconButtonToggle(fav_action)
    fav_iconToggle.listen('MDCIconButtonToggle:change', (event) => toggleWallpaperList(event.detail.isOn))

    nav_upload_button.onclick = (event) => {
        event.stopPropagation()
        const uplaod_dialog = new MDCDialog(getById('upload-wall-dialog'))

        uplaod_dialog.open()
        nav_upload_button.blur()
    }


    // nav_menu_action.onclick = (event) => {
    //     nav_menu_action.blur()
    // }

    login_action.onclick = (event) => {
        login_action.blur()
        location.href = 'login.html'
    }

    fav_action.onclick = (event) => fav_action.blur()

    const submit_btn = (event) => {
        event.preventDefault()


    }
}

const setUpMenu = async () => {
    const nav_more_btn = document.getElementById('nav-more-menu')
    const menu_logout = getById('menu-logout')
    const menu = new MDCMenu(document.querySelector('.mdc-menu'));
    const menu_user = getById('menu-user')
    const menu_user_name = getById('menu-user-name')


    if (site_state.user_id == null) {
        menu_logout.classList.add('mdc-list-item--disabled')
    } else {
        menu_logout.classList.remove('mdc-list-item--disabled')
        menu_user.classList.remove('mdc-list-item--disabled')

        const res = await fetch(api_site + `user/?id=${site_state.user_id}`)

        site_state.user = await res.json()
        menu_user_name.textContent = site_state.user.username
        console.log(site_state.user)
    }

    nav_more_btn.onclick = () => {
        nav_more_btn.blur()
        menu.open = true
        nav_more_btn.blur()
    }


    menu.listen('MDCMenu:selected', (event) => {
        const item = event.detail.item

        console.log(event.detail)
        console.log(item.id)
        switch (item.id) {
            case 'menu-logout':
                if (unsetSession()) {
                    location.reload()
                } else {
                    alert('Somthing, Went Wrong;\nPlease Close The Brwoser And Open It Again.')
                }
                break

            case 'menu-aboutus':
                location.href = 'about.html'
                break
        }
    })

}



const setUpChipSet = () => {
    getById('category-chip-container').classList.remove('disabled')
    const chipset = new MDCChipSet(document.querySelector('#category-chip'));
    const all_chipset = new MDCChipSet(document.querySelector('#category-chip-const'))
    const imageList = document.getElementById('my-image-list')
    const all_chip = document.getElementById('chip-all')
    all_chipset.setChipSelected(0, MDCChipActionType.PRIMARY, true)



    all_chipset.listen('MDCChip:interaction', (event) => {

        console.log('all_chip_set')
        console.log(event.detail)

        const event_detail = event.detail

        const evTraget = document.getElementById(event_detail.chipID)

        if (!all_chip.classList.contains(MDCChipCssClasses.SELECTED) && chipset.getSelectedChipIndexes().size != 0) {
            event.stopImmediatePropagation()
            all_chipset.setChipSelected(0, MDCChipActionType.PRIMARY, true)
            let set = chipset.getSelectedChipIndexes()
            set.forEach((i, _i, _s) => {
                chipset.setChipSelected(i, MDCChipActionType.PRIMARY, false)
            })

            site_state.active_filter = {}
            removeListFilter(site_state.active_filter)
        }

        evTraget.blur()
    })


    chipset.listen('MDCChip:interaction', (event) => {
        console.log(event.detail)
        const chipEl = getById(event.detail.chipID)
        // console.log(chipEl)

        if (chipset.getSelectedChipIndexes().size == 0 && !all_chip.classList.contains(MDCChipCssClasses.SELECTED)) {
            all_chipset.setChipSelected(0, MDCChipActionType.PRIMARY, true)
            imageList.classList.remove('selection')
        } else {
            all_chipset.setChipSelected(0, MDCChipActionType.PRIMARY, false)
            imageList.classList.add('selection')
        }


        if (chipEl.classList.contains(MDCChipCssClasses.SELECTED)) {
            switch (chipEl.id) {
                case 'chip-sky':
                    delete site_state.active_filter['sky']
                    removeListFilter(site_state.active_filter)
                    break

                case 'chip-nature':
                    delete site_state.active_filter['nature']
                    removeListFilter(site_state.active_filter)
                    break

                case 'chip-car':
                    console.log('chip-car')
                    delete site_state.active_filter['car']
                    removeListFilter(site_state.active_filter)
                    break

                case 'chip-city':
                    console.log('chip-city')
                    delete site_state.active_filter['city']
                    removeListFilter(site_state.active_filter)
                    break

                case 'chip-cat':
                    console.log('chip-cat')
                    delete site_state.active_filter['cat']
                    removeListFilter(site_state.active_filter)
                    break
            }
        } else {
            console.log({ id: chipEl.id, selected: true })
            switch (chipEl.id) {
                case 'chip-sky':
                    console.log('chip-sky')
                    site_state.active_filter['sky'] = 'sky'
                    addListFilter(site_state.active_filter)
                    break;

                case 'chip-nature':
                    console.log('chip-nature')
                    site_state.active_filter['nature'] = 'nature'
                    addListFilter(site_state.active_filter)
                    break;

                case 'chip-cars':
                    console.log('chip-car')
                    site_state.active_filter['car'] = 'car'
                    addListFilter(site_state.active_filter)
                    break;

                case 'chip-city':
                    console.log('chip-city')
                    site_state.active_filter['city'] = 'city'
                    addListFilter(site_state.active_filter)
                    break;
                case 'chip-cats':
                    console.log('chip-cat')
                    site_state.active_filter['cat'] = 'cat'
                    addListFilter(site_state.active_filter)
                    break;
            }
        }

    })


}

const addListFilter = (filters) => {
    console.log(filters)
    Object.values(site_state.image_list).forEach((image) => {
        let li = getById(`wall-list-item-${image.id}`)
        Object.values(filters).forEach((filter) => {
            if (li.classList.contains(filter)) {
                li.classList.add('enabled')
                return
            }
        })
    })
}

const removeListFilter = (filters) => {
    console.log(filters)
    
    if (Object.values(filters).length == 0) {
        const imageList = getById('my-image-list').querySelectorAll('li')
        imageList.forEach(li => {
            li.classList.remove('enabled')
        })

        getById('my-image-list').classList.remove('selection')
        return
    }

    Object.values(site_state.image_list).forEach((image) => {
        let li = getById(`wall-list-item-${image.id}`)
        let remove = 0

        Object.values(filters).forEach((filter) => {
            if (li.classList.contains(filter)) {
                remove++
            }
        })

        if (remove == 0) {
            li.classList.remove('enabled')
        }

    })
}


const setUpWallpaperList = async () => {
    const imageList = document.getElementById('my-image-list')

    const response = await fetch(api_site + "?rand")

    try {
        const images = await response.json()
        site_state.image_list = images

        if (Object.values(images).length != 0) {
            Object.values(images).forEach((value) => {
                setMasonryImageItem(value, imageList)
            })
        }
    } catch (e) {
        console.log(e)
    }

}


const toggleWallpaperList = (isOn) => {
    const image_list = getById('my-image-list')

    image_list.classList.toggle('image-list-fav', isOn)
}


const setUpFavList = async () => {
    const response = await fetch(api_site + `?fav&uid=${site_state.user_id}`)

    if (response.status == 204) {
        return []
    }

    const fav_list = await response.json()
    console.log(fav_list)

    return fav_list
}

const setUpPaidWallList = async () => {
    const response = await fetch(api_site + `user/?paid&uid=${site_state.user_id}`)

    if (response.status == 204) {
        return []
    }

    const paid_wall = await response.json()
    console.log(paid_wall)

    return paid_wall
}