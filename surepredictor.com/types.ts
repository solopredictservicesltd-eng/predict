
export interface Prediction {
  mainPrediction: string;
  mainProbability: number;
  overUnderPrediction: string;
  overUnderProbability: number;
  reasoning: string;
  expectedScore: string;
  keyStats: string[];
}

export interface TeamSuggestion {
  name: string;
  league: string;
  country: string;
}
