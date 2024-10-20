import { LoginForm } from "@/components/loginForm"

const LoginReasons: {[key: string]: string} = {
  reauth: "Veuillez vous reconnecter"
}

const getReason = (key?: string | null) => (!key) ? null : LoginReasons[key]

const LoginPage = () => {
  const searchParams = new URLSearchParams(window.location.search)

  return (
    <div className="flex flex-col lg:flex-row h-screen">
      <div className="w-full lg:w-1/2 flex items-center justify-center p-8 bg-white">
        <LoginForm reason={getReason(searchParams.get("reason"))} />
      </div>

      <div
        className="hidden lg:block w-1/2 bg-cover bg-center"
        style={{ backgroundImage: "url('/images/ciblerie_25m_close.jpg')" }}
      ></div>
    </div>
  )
}

export { LoginPage }
