<template>
  <v-dialog
    v-model="visible"
    persistent
    :max-width="options.width"
  >
    <v-card class="pa-4 text-center rounded-lg">
      <v-card-text>
        <!-- Icon Section -->
        <div
          v-if="options.icon"
          class="mb-4"
        >
          <v-avatar
            :color="iconConfig.bgColor"
            size="80"
            class="elevation-0"
          >
            <v-icon
              :color="iconConfig.iconColor"
              size="40"
            >
              {{ iconConfig.iconName }}
            </v-icon>
          </v-avatar>
        </div>

        <div
          class="text-subtitle-2 font-weight-bold mt-2 mb-5"
          :class="titleColor"
          style="position: relative;"
        >
          {{ options.title }}
          <v-icon
            v-if="options.close"
            class="close-icon"
            @click="hide"
          >
            mdi-close
          </v-icon>
        </div>
        
        <p
          v-if="options.text"
          class="font-weight-light my-4"
          :class="textAlignmentClass"
          v-html="options.text"
        />
        
        <v-textarea
          v-if="options.withTextarea"
          v-model="options.textareaValue"
          hide-details
          variant="outlined"
          :placeholder="options.textareaPlaceholder"
        />
        
        <div
          v-if="options.rejectPengadaan"
          class="mb-3"
          :style="{ display: 'flex' }"
        >
          <div>Info: </div>
          <div
            class="d-flex ml-3"
            :style="{ alignItems: 'center' }"
          >
            <div
              :style="{
                display: 'inline-block',
                width: '18px',
                height: '18px',
                borderRadius: '50%',
                backgroundColor: '#FB0000'
              }"
            />
            <div class="ml-1">
              Revisi Mayor
            </div>
          </div>
          <div
            class="d-flex ml-3"
            :style="{ alignItems: 'center' }"
          >
            <div
              :style="{
                display: 'inline-block',
                width: '18px',
                height: '18px',
                borderRadius: '50%',
                backgroundColor: '#F88BF4'
              }"
            />
            <div class="ml-1">
              Revisi Minor
            </div>
          </div>
        </div>
        
        <v-select
          v-if="options.withSelect"
          v-model="options.selectValue"
          :items="options.selectLists"
          :item-title="options.selectText"
          return-object
          density="compact"
          hide-details
          variant="outlined"
          :label="options.selectLabel"
        >
          <template
            v-if="options.rejectPengadaan"
            #selection="data"
          >
            <div class="d-flex">
              <div
                :style="{
                  width: '18px',
                  height: '18px',
                  borderRadius: '50%',
                  backgroundColor: data.item.jenisRevisi === 'Mayor' ? '#FB0000' : '#F88BF4'
                }"
              />
              <div class="ml-3">
                {{ data.item.revisiNote }}
              </div>
            </div>
          </template>
          <template
            v-if="options.rejectPengadaan"
            #item="data"
          >
            <div class="d-flex">
              <div
                :style="{
                  width: '18px',
                  height: '18px',
                  borderRadius: '50%',
                  backgroundColor: data.item.jenisRevisi === 'Mayor' ? '#FB0000' : '#F88BF4'
                }"
              />
              <div class="ml-3">
                {{ data.item.revisiNote }}
              </div>
            </div>
          </template>
        </v-select>
      </v-card-text>
      
      <!-- CARD ACTIONS: Menggunakan v-btn asli dari Vuetify -->
      <v-card-actions class="justify-center px-6 pb-2">
        <v-row v-if="type == 'confirm'">
          <v-col cols="6">
            <v-btn
              block
              variant="outlined"
              class="action-icon-btn text-indigo-600 hover:bg-indigo-50"
              :prepend-icon="options.iconBatal"
              @click="onConfirm({ confirmed: false })"
            >
              {{ options.textBatal }}
            </v-btn>
          </v-col>
          <v-col cols="6">
            <v-btn
              block
              :loading="isLoading"
              :color="options.colorConfirm"
              class="action-icon-btn text-rose-600 hover:bg-rose-50"
              variant="flat"
              :prepend-icon="options.iconConfirm"
              @click="onConfirm({ confirmed: true, data: checkData() })"
            >
              {{ options.textConfirm }}
            </v-btn>
          </v-col>
        </v-row>
        <v-btn
          v-else
          variant="flat"
          :color="options.colorConfirm"
          @click="hide()"
        >
          Ok
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
import DialogNotifPlugin from '@/plugins/globalDialogNotif'

const initialOptions = () => ({
  title: '',
  text: '',
  textPosition: '',
  width: 400,
  close: false,

  // Icon options
  icon: null, // 'warning', 'error', 'success', 'info', 'question'

  textConfirm: 'Ya',
  textBatal: 'Batal',
  iconConfirm: 'mdi-check-circle-outline',
  iconBatal: 'mdi-close',
  colorConfirm: 'primary', // Bisa: primary, warning, error, success, info
  
  withTextarea: false,
  textareaPlaceholder: 'Isi disini',
  textareaValue: '',
  withSelect: false,
  selectValue: null,
  selectLists: null,
  selectText: null,
  selectLabel: '',
  rejectPengadaan: false
})

export default {
  data() {
    return {
      isLoading: false,
      type: null,
      visible: false,
      options: initialOptions(),

      resolvePromise: undefined,
      rejectPromise: undefined,
    }
  },
  computed: {
    iconConfig() {
      const configs = {
        warning: {
          iconName: 'mdi-alert-outline',
          iconColor: 'warning',
          bgColor: '#FFF4E5'
        },
        error: {
          iconName: 'mdi-alert-circle-outline',
          iconColor: 'error',
          bgColor: '#FFEBEE'
        },
        success: {
          iconName: 'mdi-check-circle-outline',
          iconColor: 'success',
          bgColor: '#E8F5E9'
        },
        info: {
          iconName: 'mdi-information-outline',
          iconColor: 'info',
          bgColor: '#E3F2FD'
        },
        question: {
          iconName: 'mdi-help-circle-outline',
          iconColor: 'primary',
          bgColor: '#E8EAF6'
        }
      }
      
      return configs[this.options.icon] || {
        iconName: 'mdi-information-outline',
        iconColor: 'primary',
        bgColor: '#E3F2FD'
      }
    },
    
    titleColor() {
      const colors = {
        warning: 'warning--text',
        error: 'error--text',
        success: 'success--text',
        info: 'info--text',
        question: 'primary--text'
      }
      return colors[this.options.icon] || 'primary--text'
    },

    textAlignmentClass() {
      const alignments = {
        left: 'text-left',
        center: 'text-center',
        right: 'text-right',
        justify: 'text-justify'
      }
      return alignments[this.options.textPosition] || 'text-center'
    }
  },
  beforeMount() {
    const eventsBus = ['show', 'confirm']
    eventsBus.map(event => {
      DialogNotifPlugin.EventBus.$on(event, (options, resolve, reject) => {
        this[event](options, resolve, reject)
      })
    })
  },
  methods: {
    checkData() {
      if (this.options.withTextarea) return this.options.textareaValue
      else return this.options.selectValue
    },
    show(options, resolve, reject) {
      this.initWindowEvent()
      this.visible = true
      this.type = 'notif'
      this.options = { ...this.options, ...options }

      this.resolvePromise = resolve
      this.rejectPromise = reject
    },
    confirm(options, resolve, reject) {
      this.initWindowEvent()
      this.visible = true
      this.type = 'confirm'
      this.options = { ...this.options, ...options }

      this.resolvePromise = resolve
      this.rejectPromise = reject
    },
    onConfirm(data) {
      this.hide()
      setTimeout(() => {
        this.resolvePromise(data)
        window.removeEventListener('keyup', this.keyboardEvent)
      }, 300)
    },
    hide() {
      this.visible = false
      setTimeout(() => {
        this.options = initialOptions()
        this.type = false
        window.removeEventListener('keyup', this.keyboardEvent)
      }, 250)
    },
    loading() {
      this.isLoading = true
    },
    stopLoading() {
      this.isLoading = false
    },

    keyboardEvent(event) {
      if (this.type == 'confirm') {
        if (event.key == 'Escape') this.onConfirm({ confirmed: false })
        if (event.key == 'Enter') this.onConfirm({ confirmed: true, data: this.checkData() })
      } else {
        if (event.key == 'Escape') this.hide()
        if (event.key == 'Enter') this.hide()
      }
    },

    initWindowEvent() {
      window.addEventListener('keyup', this.keyboardEvent)
    },
  },
}
</script>

<style scoped>
.close-icon {
  position: absolute !important;
  right: 0;
}
</style>