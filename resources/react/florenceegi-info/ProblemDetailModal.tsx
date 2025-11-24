import React from 'react';
import './ProblemDetailModal.css';

interface ProblemDetailModalProps {
  isOpen: boolean;
  onClose: () => void;
  title: string;
  problemDetail: string;
  solutionDetail: string;
  oldWay: string;
  newWay: string;
}

const ProblemDetailModal: React.FC<ProblemDetailModalProps> = ({
  isOpen,
  onClose,
  title,
  problemDetail,
  solutionDetail,
  oldWay,
  newWay,
}) => {
  if (!isOpen) return null;

  const labels = window.florenceEgiTranslations?.modal || {};

  return (
    <div className="problem-modal-overlay" onClick={onClose}>
      <div className="problem-modal-content" onClick={(e) => e.stopPropagation()}>
        <button className="problem-modal-close" onClick={onClose} aria-label="Chiudi">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
            <path d="M18 6L6 18M6 6l12 12"/>
          </svg>
        </button>

        <div className="problem-modal-header">
          <h2>{title}</h2>
        </div>

        <div className="problem-modal-body">
          <section className="problem-section">
            <div className="section-badge problem-badge">{labels.problem_title || 'Il Problema'}</div>
            <p className="detail-text">{problemDetail}</p>
            <div className="old-way">
              <span className="way-label">❌ {labels.before || 'Prima'}:</span>
              <span className="way-text">{oldWay}</span>
            </div>
          </section>

          <section className="solution-section">
            <div className="section-badge solution-badge">{labels.solution_title || 'La Soluzione'}</div>
            <p className="detail-text">{solutionDetail}</p>
            <div className="new-way">
              <span className="way-label">✅ {labels.after || 'Adesso'}:</span>
              <span className="way-text">{newWay}</span>
            </div>
          </section>
        </div>

        <div className="problem-modal-footer">
          <button className="modal-cta-button" onClick={onClose}>
            {labels.cta_button || 'Ho Capito'}
          </button>
        </div>
      </div>
    </div>
  );
};

export default ProblemDetailModal;
