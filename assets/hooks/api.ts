class ApiError<ErrorCode extends string> extends Error {
  code?: ErrorCode
  constructor(code: ErrorCode, message: string) {
    super(message)
    this.code = code
  }
}

const symfonyErrorType =
  "https://tools.ietf.org/html/rfc2616#section-10" as const

interface SymfonyError {
  class: string
  detail: string
  status: number
  title: string
  type: typeof symfonyErrorType
}

interface CognacError<ErrorCode> {
  code: ErrorCode
  message: string
}

class Api {
  private readonly baseUrl = import.meta.env.API_BASE_URL || `${window.location.protocol}//${window.location.hostname}:${window.location.port}`

  private isSymfonyError(
    data: ReturnType<JSON["parse"]>,
  ): data is SymfonyError {
    return typeof data === "object" && data.type === symfonyErrorType
  }

  private isCognacError<ErrorCode>(
    data: ReturnType<JSON["parse"]>,
  ): data is CognacError<ErrorCode> {
    return typeof data === "object" && typeof data.code === "string"
  }

  private async request<RequestType, ErrorCode extends string, ResponseType>(
    method: "GET" | "POST",
    route: string,
    body: RequestType,
  ): Promise<ResponseType> {
    const url = new URL(route, this.baseUrl)
    const response = await fetch(url, {
      method,
      headers: {
        Accept: "application/json",
        "Content-Type": "application/json",
      },
      body: JSON.stringify(body),
    })

    if (!response.ok) {
      console.error(
        `API response error with status code ${response.status}:`,
        response,
      )
      const error = await response
        .clone()
        .json()
        .catch(async () => { throw new Error(await response.text()) })
      if (this.isCognacError<ErrorCode>(error)) {
        throw new ApiError<ErrorCode>(error.code, error.message)
      } else if (this.isSymfonyError(error)) {
        throw new Error(error.detail)
      } else {
        throw new Error(JSON.stringify(error))
      }
    }

    return response.json()
  }

  get<RequestType, ResponseType>(route: string, body: RequestType): Promise<ResponseType> {
    return this.request("GET", route, body)
  }

  post<RequestType, ResponseType>(route: string, body: RequestType): Promise<ResponseType> {
    return this.request("POST", route, body)
  }
}

const api = new Api()

export { ApiError, api }
