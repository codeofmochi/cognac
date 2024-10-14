import { useLogin } from "@/hooks/useLogin"
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome"
import {
  faCircleExclamation,
  faCircleNotch,
  faEnvelope,
} from "@fortawesome/free-solid-svg-icons"
import { FormEvent } from "react"
import { Label } from "./ui/label"
import { Input } from "./ui/input"
import { Button } from "./ui/button"
import { Alert, AlertDescription, AlertTitle } from "./ui/alert"

const LoginForm = () => {
  const login = useLogin()

  const handleSubmit = (event: FormEvent<HTMLFormElement>) => {
    event.preventDefault()
    const formData = new FormData(event.currentTarget)
    const email = formData.get("email")?.toString()
    login.mutate({ email })
  }

  return (
    <div className="max-w-md w-full">
      <div className="grid gap-2 mb-8">
        <h2 className="text-3xl font-bold">Espace membre</h2>
        <p className="text-balance text-muted-foreground">
          Saisissez votre e-mail pour vous connecter
        </p>

        {login.isSuccess && (
          <Alert className="mt-4">
            <FontAwesomeIcon icon={faEnvelope} className="h-4 w-4" />
            <AlertTitle>E-mail de connexion envoyé!</AlertTitle>
            <AlertDescription>
              <div className="grid gap-1 pt-1">
                <p>
                  Consultez votre boîte mail et cliquez sur le lien de connexion
                  dans le mail reçu. Vous pouvez ensuite fermer cette page.
                </p>
                <p className="text-xs text-muted-foreground">
                  Pas reçu d'email? Vérifiez votre boîte des spams 🔥
                </p>
              </div>
            </AlertDescription>
          </Alert>
        )}

        {login.isError && (
          <Alert className="mt-4" variant="destructive">
            <FontAwesomeIcon icon={faCircleExclamation} className="h-4 w-4" />
            <LoginErrorContent error={login.error} />
          </Alert>
        )}
      </div>

      <form onSubmit={handleSubmit}>
        <div className="grid gap-4">
          <div className="grid gap-2">
            <Label htmlFor="email">E-mail</Label>
            <Input
              id="email"
              type="email"
              name="email"
              placeholder="jean.dupont@example.com"
              disabled={login.isLoading}
              required
            />
          </div>

          <Button type="submit" className="w-full" disabled={login.isLoading}>
            {login.isLoading && (
              <FontAwesomeIcon
                icon={faCircleNotch}
                className="mr-2 animate-spin"
              />
            )}
            {login.isSuccess ? "Renvoyer un lien de connexion" : "Connexion"}
          </Button>
        </div>
      </form>
    </div>
  )
}

const LoginErrorContent = ({
  error,
}: {
  error: NonNullable<ReturnType<typeof useLogin>["error"]>
}) => {
  switch (error.code) {
    case "USER_NOT_FOUND":
      return (
        <>
          <AlertTitle>Utilisateur non trouvé</AlertTitle>
          <AlertDescription>
            L'email saisi n'existe dans le système. Veuillez vérifier si l'adresse est correcte, sinon contactez un administrateur.
          </AlertDescription>
        </>
      )
    case undefined:
      return (
        <>
          <AlertTitle>Une erreur est survenue</AlertTitle>
          <AlertDescription>
            Veuillez réessayer ou contacter un administrateur.
          </AlertDescription>
        </>
      )
    default:
      // exhaustive switch check
      return error.code satisfies never
  }
}

export { LoginForm }
