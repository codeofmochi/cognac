import { QueryClientProvider } from "react-query"
import { queryClient } from "./hooks/queryClient"
import { LoginPage } from "./pages/loginPage"

function App() {
  return (
    <QueryClientProvider client={queryClient}>
      <LoginPage />
    </QueryClientProvider>
  )
}

export default App
