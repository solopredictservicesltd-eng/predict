
import React, { useState, useEffect, useRef } from 'react';
import { getTeamSuggestions } from '../services/geminiService';
import { TeamSuggestion } from '../types';

interface AutocompleteProps {
  label: string;
  value: string;
  onChange: (val: string) => void;
  placeholder: string;
  icon: string;
}

const Autocomplete: React.FC<AutocompleteProps> = ({ label, value, onChange, placeholder, icon }) => {
  const [suggestions, setSuggestions] = useState<TeamSuggestion[]>([]);
  const [isOpen, setIsOpen] = useState(false);
  const [isLoading, setIsLoading] = useState(false);
  const [selectedByUser, setSelectedByUser] = useState(false);
  const containerRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    // If user just clicked a suggestion, don't trigger a new search
    if (selectedByUser) {
      setSelectedByUser(false);
      return;
    }

    // Faster debounce (300ms) for better UX
    const timer = setTimeout(async () => {
      if (value.length >= 3) {
        setIsLoading(true);
        const results = await getTeamSuggestions(value);
        setSuggestions(results);
        setIsOpen(results.length > 0);
        setIsLoading(false);
      } else {
        setSuggestions([]);
        setIsOpen(false);
      }
    }, 300);

    return () => clearTimeout(timer);
  }, [value]);

  useEffect(() => {
    const handleClickOutside = (event: MouseEvent) => {
      if (containerRef.current && !containerRef.current.contains(event.target as Node)) {
        setIsOpen(false);
      }
    };
    document.addEventListener('mousedown', handleClickOutside);
    return () => document.removeEventListener('mousedown', handleClickOutside);
  }, []);

  return (
    <div className="relative flex-1" ref={containerRef}>
      <label className="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">
        {label}
      </label>
      <div className="relative">
        <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
          <i className={`${icon} text-emerald-600`}></i>
        </div>
        <input
          type="text"
          value={value}
          onChange={(e) => {
            setSelectedByUser(false);
            onChange(e.target.value);
          }}
          placeholder={placeholder}
          className="w-full bg-slate-50 border border-slate-200 text-slate-900 rounded-xl py-3 pl-10 pr-4 focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition-all placeholder:text-slate-400"
        />
        {isLoading && (
          <div className="absolute inset-y-0 right-3 flex items-center">
            <div className="animate-spin h-4 w-4 border-2 border-emerald-600 border-t-transparent rounded-full"></div>
          </div>
        )}
      </div>

      {isOpen && (
        <ul className="absolute z-50 w-full mt-2 bg-white border border-slate-200 rounded-xl shadow-xl overflow-hidden max-h-60 overflow-y-auto">
          {suggestions.map((suggestion, index) => (
            <li
              key={index}
              onClick={() => {
                setSelectedByUser(true);
                onChange(suggestion.name);
                setIsOpen(false);
              }}
              className="px-4 py-3 hover:bg-slate-50 cursor-pointer border-b border-slate-100 last:border-0 transition-colors"
            >
              <div className="font-medium text-slate-900">{suggestion.name}</div>
              <div className="text-xs text-slate-500 flex justify-between">
                <span>{suggestion.league}</span>
                <span className="italic">{suggestion.country}</span>
              </div>
            </li>
          ))}
        </ul>
      )}
    </div>
  );
};

export default Autocomplete;
