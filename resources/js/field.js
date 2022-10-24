import IndexField from './components/IndexField'
import DetailField from './components/DetailField'
import FormField from './components/FormField'

Nova.booting((app, store) => {
    app.component('index-items', IndexField)
    app.component('detail-items', DetailField)
    app.component('form-items', FormField)
})
