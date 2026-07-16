import DialogNotifPlugin from '@/plugins/globalDialogNotif'

export default function registerGlobalUsage(app) {
  app.use(DialogNotifPlugin)
}
