
import React from 'react';
import { Prediction } from '../types';
import { PieChart, Pie, Cell, ResponsiveContainer, Tooltip } from 'recharts';

interface PredictionCardProps {
  prediction: Prediction;
  homeTeam: string;
  awayTeam: string;
}

const PredictionCard: React.FC<PredictionCardProps> = ({ prediction, homeTeam, awayTeam }) => {
  const mainData = [
    { name: 'Probability', value: prediction.mainProbability },
    { name: 'Remaining', value: 100 - prediction.mainProbability },
  ];

  const overUnderData = [
    { name: 'Probability', value: prediction.overUnderProbability },
    { name: 'Remaining', value: 100 - prediction.overUnderProbability },
  ];

  const COLORS = ['#059669', '#f8fafc']; // Emerald-600 and Slate-50

  return (
    <div className="bg-white border border-slate-200 rounded-3xl overflow-hidden shadow-2xl animate-in fade-in slide-in-from-bottom-6 duration-1000 ease-out">
      {/* Header with improved contrast */}
      <div className="bg-gradient-to-br from-emerald-600 via-emerald-500 to-green-500 p-8 text-center relative overflow-hidden">
        <div className="absolute top-0 left-0 w-full h-full opacity-10 pointer-events-none">
          <i className="fas fa-futbol text-9xl -ml-10 -mt-10 rotate-12"></i>
        </div>
        <h2 className="text-3xl font-black text-white mb-2 relative z-10 tracking-tight">Match Forecast</h2>
        <div className="relative z-10 flex items-center justify-center gap-3 mt-4">
          <span className="bg-white/20 backdrop-blur-md px-4 py-1.5 rounded-xl text-white font-bold text-lg border border-white/30">
            {homeTeam}
          </span>
          <span className="text-white/70 font-black italic">VS</span>
          <span className="bg-white/20 backdrop-blur-md px-4 py-1.5 rounded-xl text-white font-bold text-lg border border-white/30">
            {awayTeam}
          </span>
        </div>
      </div>

      <div className="p-8 md:p-12 space-y-12">
        {/* Probability Charts Section */}
        <div className="grid grid-cols-1 md:grid-cols-2 gap-12">
          {/* Main Outcome Chart */}
          <div className="flex flex-col items-center text-center space-y-6 animate-in fade-in zoom-in-95 duration-700 delay-200">
            <div className="space-y-1">
              <span className="text-slate-400 text-xs font-bold uppercase tracking-widest">Confidence Level</span>
              <h3 className="text-xl font-extrabold text-slate-800 tracking-tight">Main Outcome</h3>
            </div>
            
            <div className="h-48 w-48 relative">
              <ResponsiveContainer width="100%" height="100%">
                <PieChart>
                  <Pie
                    data={mainData}
                    innerRadius={70}
                    outerRadius={90}
                    paddingAngle={0}
                    dataKey="value"
                    stroke="none"
                    startAngle={90}
                    endAngle={-270}
                  >
                    {mainData.map((entry, index) => (
                      <Cell key={`cell-${index}`} fill={index === 0 ? COLORS[0] : COLORS[1]} />
                    ))}
                  </Pie>
                  <Tooltip cursor={{ stroke: 'none' }} />
                </PieChart>
              </ResponsiveContainer>
              <div className="absolute inset-0 flex flex-col items-center justify-center">
                <span className="text-3xl font-black text-emerald-600">{prediction.mainProbability}%</span>
                <span className="text-[10px] text-slate-400 font-bold uppercase">Probability</span>
              </div>
            </div>
            
            <div className="bg-emerald-50 px-6 py-3 rounded-2xl border border-emerald-100 transform transition-transform hover:scale-105 duration-300">
              <span className="text-2xl font-black text-emerald-700 uppercase tracking-tighter">
                {prediction.mainPrediction}
              </span>
            </div>
          </div>

          {/* Over/Under Chart */}
          <div className="flex flex-col items-center text-center space-y-6 animate-in fade-in zoom-in-95 duration-700 delay-400">
            <div className="space-y-1">
              <span className="text-slate-400 text-xs font-bold uppercase tracking-widest">Goals Analysis</span>
              <h3 className="text-xl font-extrabold text-slate-800 tracking-tight">Over/Under 2.5</h3>
            </div>

            <div className="h-48 w-48 relative">
              <ResponsiveContainer width="100%" height="100%">
                <PieChart>
                  <Pie
                    data={overUnderData}
                    innerRadius={70}
                    outerRadius={90}
                    paddingAngle={0}
                    dataKey="value"
                    stroke="none"
                    startAngle={90}
                    endAngle={-270}
                  >
                    {overUnderData.map((entry, index) => (
                      <Cell 
                        key={`cell-${index}`} 
                        fill={index === 0 ? (prediction.overUnderPrediction.includes('Over') ? '#3b82f6' : '#f59e0b') : COLORS[1]} 
                      />
                    ))}
                  </Pie>
                  <Tooltip />
                </PieChart>
              </ResponsiveContainer>
              <div className="absolute inset-0 flex flex-col items-center justify-center">
                <span className={`text-3xl font-black ${prediction.overUnderPrediction.includes('Over') ? 'text-blue-600' : 'text-amber-600'}`}>
                  {prediction.overUnderProbability}%
                </span>
                <span className="text-[10px] text-slate-400 font-bold uppercase">Confidence</span>
              </div>
            </div>

            <div className={`px-6 py-3 rounded-2xl border transform transition-transform hover:scale-105 duration-300 ${
              prediction.overUnderPrediction.includes('Over') 
                ? 'bg-blue-50 border-blue-100 text-blue-700' 
                : 'bg-amber-50 border-amber-100 text-amber-700'
            }`}>
              <span className="text-2xl font-black uppercase tracking-tighter">
                {prediction.overUnderPrediction}
              </span>
            </div>
          </div>
        </div>

        {/* Tactical & Detailed Analysis */}
        <div className="space-y-10 animate-in fade-in slide-in-from-bottom-4 duration-700 delay-600">
          <div className="flex flex-col items-center">
             <div className="group relative">
                <div className="absolute -inset-1 bg-gradient-to-r from-emerald-600 to-green-500 rounded-full blur opacity-25 group-hover:opacity-50 transition duration-1000 group-hover:duration-200"></div>
                <div className="relative px-8 py-3 bg-white border border-slate-100 rounded-full flex items-center gap-4">
                  <span className="text-slate-400 font-semibold uppercase text-xs tracking-widest">Expected Score</span>
                  <span className="text-slate-900 font-black text-3xl tracking-tighter">{prediction.expectedScore}</span>
                </div>
             </div>
          </div>

          <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div className="bg-slate-50 p-8 rounded-[2rem] border border-slate-100 relative group transition-all duration-300 hover:shadow-lg">
              <div className="absolute -top-4 left-6 bg-emerald-600 text-white p-2 rounded-xl shadow-lg">
                <i className="fas fa-brain"></i>
              </div>
              <h3 className="text-slate-800 font-black text-lg mb-4 mt-2 uppercase tracking-tight">AI Reasoning</h3>
              <p className="text-slate-600 leading-relaxed font-medium">
                {prediction.reasoning}
              </p>
            </div>

            <div className="bg-slate-50 p-8 rounded-[2rem] border border-slate-100 relative group transition-all duration-300 hover:shadow-lg">
              <div className="absolute -top-4 left-6 bg-slate-800 text-white p-2 rounded-xl shadow-lg">
                <i className="fas fa-chart-line"></i>
              </div>
              <h3 className="text-slate-800 font-black text-lg mb-4 mt-2 uppercase tracking-tight">Key Insights</h3>
              <ul className="space-y-4">
                {prediction.keyStats.map((stat, idx) => (
                  <li key={idx} className="flex items-center text-slate-600 bg-white/50 p-3 rounded-xl border border-white/80 transition-transform hover:translate-x-1 duration-200">
                    <span className="bg-emerald-100 text-emerald-600 h-6 w-6 rounded-full flex items-center justify-center text-[10px] font-bold mr-3 shrink-0">
                      {idx + 1}
                    </span>
                    <span className="font-medium text-sm">{stat}</span>
                  </li>
                ))}
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default PredictionCard;
