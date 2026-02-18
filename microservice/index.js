const express = require('express')
const { processClipPayload } = require('./src/clipProcessor')

const app = express()
const port = Number(process.env.PORT || 3001)

app.use(express.json())

app.get('/health', (_request, response) => {
  response.json({
    status: 'ok',
  })
})

app.post('/api/process-clip', (request, response) => {
  const result = processClipPayload(request.body)

  if (!result.valid) {
    return response.status(422).json({
      message: result.error,
    })
  }

  return response.json({
    data: result.processed,
  })
})

if (require.main === module) {
  app.listen(port, () => {
    console.log(`Clip processor listening on http://localhost:${port}`)
  })
}

module.exports = app
