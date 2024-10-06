
type UseCaseInput = any;

type UseCaseOutput = any;

export default interface UseCase {
    execute(input?: UseCaseInput): UseCaseOutput | void
}