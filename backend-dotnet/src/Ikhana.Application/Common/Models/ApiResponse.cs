using System.Text.Json.Serialization;

namespace Ikhana.Application.Common.Models;

public class ApiResponse<T>
{
    public bool Success { get; }
    public string Message { get; }

    [JsonIgnore(Condition = JsonIgnoreCondition.WhenWritingNull)]
    public T? Data { get; }

    public ApiResponse(bool success, T? data, string message)
    {
        Success = success;
        Data = data;
        Message = message;
    }

    public static ApiResponse<T> Ok(T data, string message = "Success") =>
        new(true, data, message);

    public static ApiResponse<T> Fail(string message) =>
        new(false, default, message);
}
