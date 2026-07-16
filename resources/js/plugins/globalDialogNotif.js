import DialogNotif from '@/base/components/GlobalDialogNotifBase.vue'

// Simple Event Emitter for Vue 3 replacement of new Vue()
class EventBus {
  constructor() {
    this.events = {}
  }

  $on(event, callback) {
    if (!this.events[event]) {
      this.events[event] = []
    }
    this.events[event].push(callback)
  }

  $emit(event, ...args) {
    if (this.events[event]) {
      this.events[event].forEach(callback => callback(...args))
    }
  }
}

const bus = new EventBus()

const DialogNotifPlugin = {
  // Expose the bus so the component can subscribe
  EventBus: bus,

  install(app, options) {
    // Register global component
    app.component('DialogNotif', DialogNotif)

    const eventsBus = ['show', 'confirm']
    
    // Create global property $dialogNotif
    app.config.globalProperties.$dialogNotif = eventsBus.reduce((acc, event) => {
      return { ...acc, [event]: function(options) {
        return new Promise((resolve, reject) => {
          bus.$emit(event, options, resolve, reject)
        })
      } }
    }, {})
  }
}

export default DialogNotifPlugin
