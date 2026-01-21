
import React, { useState } from 'react';
import Autocomplete from './components/Autocomplete';
import PredictionCard from './components/PredictionCard';
import { getPrediction } from './services/geminiService';
import { Prediction } from './types';

const App: React.FC = () => {
  const [homeTeam, setHomeTeam] = useState('');
  const [awayTeam, setAwayTeam] = useState('');
  const [prediction, setPrediction] = useState<Prediction | null>(null);
  const [isLoading, setIsLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);

  const handlePredict = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!homeTeam || !awayTeam) return;

    setIsLoading(true);
    setError(null);
    setPrediction(null);

    try {
      const result = await getPrediction(homeTeam, awayTeam);
      setPrediction(result);
    } catch (err: any) {
      setError(err.message || 'Something went wrong. Please check your internet and try again.');
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <div className="max-w-5xl mx-auto px-4 py-12 md:py-20">
      {/* Header */}
      <header className="text-center mb-16 space-y-4">
        <div className="inline-flex items-center justify-center p-3 bg-emerald-50 rounded-2xl mb-4 border border-emerald-100">
          <i className="fas fa-chart-line text-3xl text-emerald-600"></i>
        </div>
        <h1 className="text-4xl md:text-6xl font-black tracking-tight text-slate-900">
          surepredictor<span className="text-emerald-600">.com</span>
        </h1>
        <p className="text-slate-500 text-lg md:text-xl max-w-2xl mx-auto font-light">
          Global football insights powered by advanced AI. Real-time accuracy for every major league and tournament.
        </p>
      </header>

      {/* Prediction Input Form */}
      <div className="bg-white border border-slate-200 p-8 rounded-3xl shadow-xl mb-12 relative overflow-hidden">
        <div className="absolute top-0 right-0 w-64 h-64 bg-emerald-500/5 blur-[100px] -mr-32 -mt-32 rounded-full"></div>
        <form onSubmit={handlePredict} className="relative z-10 space-y-8">
          <div className="flex flex-col md:flex-row gap-6 items-center">
            <Autocomplete
              label="Home Team"
              value={homeTeam}
              onChange={setHomeTeam}
              placeholder="e.g. Real Madrid"
              icon="fas fa-house"
            />
            <div className="hidden md:flex flex-col items-center justify-center mt-6">
              <span className="text-slate-300 font-bold italic">VS</span>
            </div>
            <Autocomplete
              label="Away Team"
              value={awayTeam}
              onChange={setAwayTeam}
              placeholder="e.g. Manchester City"
              icon="fas fa-plane"
            />
          </div>

          <div className="flex flex-col items-center">
            <button
              type="submit"
              disabled={isLoading || !homeTeam || !awayTeam}
              className="w-full md:w-auto px-12 py-4 bg-emerald-600 hover:bg-emerald-500 disabled:bg-slate-100 disabled:text-slate-400 disabled:cursor-not-allowed text-white font-bold text-lg rounded-2xl transition-all shadow-lg shadow-emerald-600/20 active:scale-95 flex items-center justify-center gap-3"
            >
              {isLoading ? (
                <>
                  <div className="animate-spin h-5 w-5 border-3 border-white border-t-transparent rounded-full"></div>
                  AI Analyzing Matchup...
                </>
              ) : (
                <>
                  <i className="fas fa-bolt"></i>
                  Get Prediction
                </>
              )}
            </button>
            <p className="mt-4 text-xs text-slate-400 italic">
              *AI analysis based on current squad data and latest league standings.
            </p>
          </div>
        </form>
      </div>

      {/* Results Section */}
      <div className="mt-12 min-h-[300px]">
        {error && (
          <div className="bg-red-50 border border-red-100 text-red-600 p-6 rounded-2xl flex items-center gap-4 animate-in fade-in zoom-in-95 duration-300">
            <i className="fas fa-circle-exclamation text-xl"></i>
            <p>{error}</p>
          </div>
        )}

        {isLoading && !prediction && (
          <div className="flex flex-col items-center justify-center space-y-6 py-20">
             <div className="relative h-16 w-16">
                <div className="absolute inset-0 border-4 border-emerald-100 rounded-full"></div>
                <div className="absolute inset-0 border-4 border-emerald-600 border-t-transparent rounded-full animate-spin"></div>
             </div>
             <p className="text-slate-500 animate-pulse text-center">
               Synchronizing latest team data and tactical performance stats...
             </p>
          </div>
        )}

        {prediction && (
          <PredictionCard 
            prediction={prediction} 
            homeTeam={homeTeam} 
            awayTeam={awayTeam} 
          />
        )}

        {!prediction && !isLoading && !error && (
          <div className="flex flex-col items-center justify-center py-20 border-2 border-dashed border-slate-100 rounded-3xl">
            <i className="fas fa-chart-bar text-4xl text-slate-200 mb-4"></i>
            <p className="text-slate-400">Search for teams to generate professional AI analysis</p>
          </div>
        )}
      </div>

      {/* Footer info */}
      <footer className="mt-24 pt-8 border-t border-slate-100 text-center text-slate-400 text-sm">
        <p>© {new Date().getFullYear()} surepredictor.com. All predictions generated by Gemini Pro.</p>
        <p className="mt-2 text-xs">Data dynamically calibrated for the current global football calendar.</p>
      </footer>
    </div>
  );
};

export default App;
