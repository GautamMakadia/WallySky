import { MDCDialog } from '@material/dialog'
import { getById } from './comman.js'

export const setUpMaxWallDialog = () => {

    const payment_content = getById('payment-content')
    const wall_down_action = getById('dialog-down-btn')
    const max_dialog = new MDCDialog(getById('max-wall-dialog'))

    wall_down_action.onclick = (event) => {
        event.stopPropagation()
        payment_content.classList.remove('disabled')
    }

    max_dialog.listen('MDCDialog:closed', (event) => {
        payment_content.classList.add('disabled')        
    })
 
}
