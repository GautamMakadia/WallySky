import { MDCDialog } from '@material/dialog'
import { getById } from './comman.js'
import { image_site } from '../config/site.conf.js'
import { site_state } from '../index.js'

let file = null
const drop_aria = getById('drop-aria')

export const setUpUploadDialog = () => {
    
    const upload_dialogEl = getById('upload-wall-dialog')
    const upload_dialog = new MDCDialog(upload_dialogEl)

    const drog_aria_lable = getById('drop-aria-lable')
    const select_btn = getById('select-wall-btn')
    const upload_form = getById('upload-form')
    const upload_btn = getById('upload')
    const upload_img = getById('upload-img')
    const upload_form_container = getById('upload-form-container')
    const file_input = getById('wallpaper-input')



    select_btn.onclick = (event) => {
        event.stopPropagation()
        file_input.click()
    }

    file_input.addEventListener('change', (event) => {
        file = file_input.files[0]
        showUploadForm(file)
    })

    drop_aria.ondragenter = (event) => event.preventDefault()
    drop_aria.ondragend = (event) =>  {
        event.preventDefault()
        upload_dialogEl.classList.remove('dragover')
    }

    drop_aria.ondragover = (event) => {
        event.preventDefault()
        // event.stopPropagation()
        upload_dialogEl.classList.add('dragover')
        drog_aria_lable.textContent = 'Drop Here To Upload'
    }

    drop_aria.ondragleave = (event) => {
        event.preventDefault()
        upload_dialogEl.classList.remove('dragover')
        upload_dialogEl.classList.add('dragleave')
        drog_aria_lable.textContent = "Drag & Drop to Upload File"
        upload_dialogEl.classList.remove('dragleave')
    }

    drop_aria.ondrop = (event) => {
        event.preventDefault()
        
        upload_dialogEl.classList.remove('dragover')
        // event.stopPropagation()
        file = event.dataTransfer.files[0]

        console.log(file.type)
        file_input.files = event.dataTransfer.files
        showUploadForm(file)
    }


    upload_dialog.listen('MDCDialog:closed', (event)=> {
        event.preventDefault()
        upload_form_container.classList.add('disabled')
        upload_img.src = ''
        upload_form.reset()
        drop_aria.classList.remove('disabled')
    })

    upload_form.onsubmit = async (event) => {
        event.preventDefault()

        let upload_form_data = new FormData(event.currentTarget, upload_btn)
        upload_form_data.append('uid', site_state.user_id)

        console.log(Array.from(upload_form_data))


        const upload_responce = await fetch(image_site+'api/', {
            method: "POST",
            body: upload_form_data
        })

        if (upload_responce.status == 500) {
            // console.log(upload_responce)
            const res = await upload_responce.json()
            console.log(res)
            alert(JSON.stringify(res))
            
        } else if (upload_responce.status == 201) {
            const res = await upload_responce.json()
            console.log(res)
            alert(JSON.stringify(res))
        }

        upload_form.reset()
        upload_form_container.classList.add('disabled')
        drop_aria.classList.remove('disabled')
    }


}


const showUploadForm = () => {

    const fileType = file.type; //getting selected file type
    const validExtensions = ["image/jpeg", "image/jpg", "image/png", "image/webp", "image/avif"]
    const upload_form_container = getById('upload-form-container')

    if (validExtensions.includes(fileType)) {
        const fileReader = new FileReader();

        fileReader.onload = (event) => {
            let url = fileReader.result
            const upload_img = getById('upload-img')

            upload_img.src = url

            upload_form_container.classList.remove('disabled')
            drop_aria.classList.add('disabled')
        }

        fileReader.readAsDataURL(file)

    } else {
        alert("This is not an Image File!");
        file = null
    }

    
}



