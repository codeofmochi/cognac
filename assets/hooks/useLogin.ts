import { useMutation } from "react-query"
import { api, ApiError } from "./api"

interface LoginCredentials {
  email?: string
}

interface LoginResponse {
  message: string
}

type LoginErrorCode = "USER_NOT_FOUND"

async function login(credentials: LoginCredentials): Promise<LoginResponse> {
  return api.post("/login", credentials)
}

function useLogin() {
  return useMutation<LoginResponse, ApiError<LoginErrorCode>, LoginCredentials>(login)
}

export { useLogin }