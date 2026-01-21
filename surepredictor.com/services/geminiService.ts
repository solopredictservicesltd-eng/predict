
import { GoogleGenAI, Type } from "@google/genai";
import { Prediction, TeamSuggestion } from "../types";

const ai = new GoogleGenAI({ apiKey: process.env.API_KEY || '' });

/**
 * Calculates the current football season string (e.g., "2025/2026").
 * Most major global leagues start their new season cycle around July/August.
 * If the current month is July (6) or later, we are in the start of the 'Year / Year+1' season.
 * Otherwise, we are in the 'Year-1 / Year' season.
 */
const getCurrentSeasonRange = () => {
  const now = new Date();
  const year = now.getFullYear();
  const month = now.getMonth(); 
  
  // Transition month set to July (6) to capture the 2025/2026 cycle correctly in 2025
  const seasonStartYear = month >= 6 ? year : year - 1;
  return `${seasonStartYear}/${seasonStartYear + 1}`;
};

export const getTeamSuggestions = async (input: string): Promise<TeamSuggestion[]> => {
  if (input.length < 3) return [];
  const season = getCurrentSeasonRange();

  try {
    const response = await ai.models.generateContent({
      model: "gemini-flash-lite-latest",
      contents: `Search for professional football teams matching "${input}". 
      DATA ACCURACY: Return the league the team is playing in for the ${season} season.
      CRITICAL: For ${season}, verify promotion/relegation. 
      Example: If a team was promoted to the Premier League for ${season}, list 'Premier League'.
      Return 5 results as JSON.`,
      config: {
        thinkingConfig: { thinkingBudget: 0 },
        responseMimeType: "application/json",
        responseSchema: {
          type: Type.ARRAY,
          items: {
            type: Type.OBJECT,
            properties: {
              name: { type: Type.STRING },
              league: { type: Type.STRING },
              country: { type: Type.STRING },
            },
            required: ["name", "league", "country"],
          },
        },
      },
    });

    return JSON.parse(response.text || "[]");
  } catch (error) {
    console.error("Error fetching suggestions:", error);
    return [];
  }
};

export const getPrediction = async (home: string, away: string): Promise<Prediction> => {
  const season = getCurrentSeasonRange();
  try {
    const response = await ai.models.generateContent({
      model: "gemini-3-pro-preview",
      contents: `Perform a deep tactical analysis for ${home} (Home) vs ${away} (Away) for the ${season} season.
      Verify their ${season} league membership and latest squad transfers.
      
      CRITICAL PROBABILITY GUIDELINES:
      - Provide a probability percentage from 1 to 100.
      - 50% means a coin flip.
      - 70-80% is high confidence.
      - 90%+ is near certain.
      - Use whole numbers for clarity.

      Provide:
      1. Main outcome (1X2)
      2. Over/Under 2.5 goals prediction
      3. Probabilities for both (1-100)
      4. Detailed reasoning including tactical shifts for the ${season} campaign
      5. Expected final score
      6. Key match stats for ${season}.`,
      config: {
        thinkingConfig: { thinkingBudget: 2000 },
        responseMimeType: "application/json",
        responseSchema: {
          type: Type.OBJECT,
          properties: {
            mainPrediction: { type: Type.STRING },
            mainProbability: { type: Type.NUMBER, description: "A percentage value from 1 to 100" },
            overUnderPrediction: { type: Type.STRING },
            overUnderProbability: { type: Type.NUMBER, description: "A percentage value from 1 to 100" },
            reasoning: { type: Type.STRING },
            expectedScore: { type: Type.STRING },
            keyStats: { 
              type: Type.ARRAY, 
              items: { type: Type.STRING }
            },
          },
          required: ["mainPrediction", "mainProbability", "overUnderPrediction", "overUnderProbability", "reasoning", "expectedScore", "keyStats"],
        },
      },
    });

    return JSON.parse(response.text || "{}");
  } catch (error) {
    console.error("Error fetching prediction:", error);
    throw new Error(`Failed to generate prediction for the ${season} season.`);
  }
};
