const formatDate = (dateStr) => {
  if (!dateStr) return '-'
  const date = new Date(dateStr)
  return new Intl.DateTimeFormat('id-ID', { dateStyle: 'medium' }).format(date)
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
  formatNumber,
  handleInputNumber
}