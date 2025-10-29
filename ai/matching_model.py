from sklearn.metrics.pairwise import cosine_similarity
from sklearn.feature_extraction.text import TfidfVectorizer
import sys
import json

# Simple matching
def match_score(student_profile, scholarship_desc):
    vectorizer = TfidfVectorizer()
    tfidf_matrix = vectorizer.fit_transform([student_profile['skills'] + ' ' + student_profile['research_interest'], scholarship_desc])
    score = cosine_similarity(tfidf_matrix[0:1], tfidf_matrix[1:2])[0][0]
    return {'score': score * 100}

if __name__ == "__main__":
    input_data = json.loads(sys.argv[1])
    result = match_score(input_data['student'], input_data['scholarship'])
    print(json.dumps(result))