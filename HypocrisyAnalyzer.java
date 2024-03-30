import okhttp3.*;
import org.json.JSONArray;
import org.json.JSONObject;

import java.io.IOException;

public class HypocrisyAnalyzer {
    private static final String OPENAI_API_KEY = "your_openai_api_key_here";

    public static String analyzeHypocrisy(String text) throws IOException {
        OkHttpClient client = new OkHttpClient();

        MediaType mediaType = MediaType.parse("application/json");
        RequestBody body = RequestBody.create(mediaType, "{\"prompt\": \"" +
                "Analyze the text below for hypocrisy. Look for contradictions, double standards, or hypocritical statements.  The output should start with a score based on the following formula: Score = (Statements_made / Hypocritical_statements_found) * 100.  After the score, a very short summary of the text should be produced.  After the summary, list the Hypocritical statements and the explanation of why it is hypocritical.  Do not limit evaluation to the document - Use any information available for the determination of hypocrisy in the text.\n\n" +
                text + "\", \"max_tokens\": 150}");

        Request request = new Request.Builder()
                .url("https://api.openai.com/v1/engines/text-davinci-003/completions")
                .post(body)
                .addHeader("Content-Type", "application/json")
                .addHeader("Authorization", "Bearer " + OPENAI_API_KEY)
                .build();

        try (Response response = client.newCall(request).execute()) {
            if (!response.isSuccessful()) throw new IOException("Unexpected code " + response);

            JSONObject jsonResponse = new JSONObject(response.body().string());
            JSONArray choices = jsonResponse.getJSONArray("choices");
            if (choices.length() > 0) {
                JSONObject choice = choices.getJSONObject(0);
                return choice.getString("text");
            } else {
                return "No response or empty response from the API.";
            }
        }
    }
}

