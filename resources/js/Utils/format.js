const formatDate = (dateStr) => {
  if (!dateStr) return '-'
  const date = new Date(dateStr)
  return new Intl.DateTimeFormat('id-ID', { dateStyle: 'medium' }).format(date)
}

const getTodayString = () => {
  const d = new Date()
  const year = d.getFullYear()
  const month = String(d.getMonth() + 1).padStart(2, '0')
  const day = String(d.getDate()).padStart(2, '0')
  return `${year}-${month}-${day}`
}

const formatNumber = (num) => {
  return new Intl.NumberFormat('id-ID').format(num || 0)
}

const handleInputNumber = (rawValue, fieldName, formObj) => {
  let cleanNumber = rawValue.replace(/\D/g, '')
  
  if (cleanNumber.length > 1 && cleanNumber.startsWith('0')) {
    cleanNumber = cleanNumber.replace(/^0+/, '')
  }
  
  formObj[fieldName] = cleanNumber ? parseInt(cleanNumber, 10) : 0
}

export default {
  formatDate,
  getTodayString,
  formatNumber,
  handleInputNumber
}