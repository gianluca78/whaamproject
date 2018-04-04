setwd("/var/www/html/whaam-master/app/data/R/assessment/")

tauSystem<-function(){
  args <- commandArgs(trailingOnly = TRUE)
  require("Kendall")
  require("RJSONIO")
  source(args)
  nameTime = "TIME"
  namePhase = "DUMMYPHASE" 
  nameDV = "DV"
  Aphase = 0
  Bphase = 1
  
  data1<-matrix(,ncol = 2,nrow=length(data))
  data1<-as.data.frame(data1)
  data1[,1]<-data
  data1[,2]<-fase

  colnames(data1)<-c("DV","PHASE")
  data1[data1[,"PHASE"] %in% "A","DUMMYPHASE"]=0
  data1[data1[,"PHASE"] %in% "B","DUMMYPHASE"]=1
  data1[,"TIME"]=as.integer(rownames(data1))

  #? Taub SDtau p_exact?  rpudplus.
  
  #Matrix preparation
  dime <- dim(data1)[1]
  coln <- 0
  compa <- matrix(NA, nrow = dime, ncol = dime)

  for (i in 1:dim(data1)[1]) {
    for (l in 1:dim(data1)[1]) {
      compa[i, l] <- data1[dime + 1 - l, "DV"] - data1[i, "DV"]
      coln[l] <- data1[dime + 1 - l, "DV"]
    }
  }
  
  colnames(compa) <- coln
  rownames(compa) <- data1[, "DV"]
  
  NuA <- sum(data1[, namePhase] == Aphase)
  NuB <- sum(data1[, namePhase] == Bphase)

  findindices <- function(mat, rp1, cp2, tri = FALSE) {

    mat <- mat[, rev(seq.int(ncol(mat)))]
    mat[lower.tri(mat)] <- NA
    mat <- mat[, rev(seq_len(ncol(mat)))]
    
    
    if (tri == FALSE) {
      matriAB <- mat[1:NuA, 1:NuB]
      npairsAB <- 0
      nposAB <- 0
      nnegAB <- 0
      
      for (i in 1:dim(matriAB)[1]) {
        for (k in 1:dim(matriAB)[2]) {
          npairsAB <- npairsAB + 1
          if (matriAB[i, k] > 0) {
            nposAB = nposAB + 1
          }
          if (matriAB[i, k] < 0) {
            nnegAB = nnegAB + 1
          }
        }
      }
      
      ro <- rownames(matriAB)
      colo <- colnames(matriAB)
      ze <- rep(0, length(ro))
      un <- rep(1, length(colo))
      sasa <- c(ro, colo)
      ke <- Kendall(c(ro, colo), c(ze, un))
      kefu <- c(ro, colo)
      #	print(kefu) 
      kefu <- Kendall(kefu, 1:length(kefu)) 
      #	print(kefu) 
      varsf <- kefu[[5]]
      pcf <- kefu[[2]]
      
      tt <- c(rep(0, length(ro)), (length(ro) + 1):length(sasa))
      keAu <- Kendall(sasa, tt)
      varsA1 <- keAu[[5]]
      pcA1 <- keAu[[2]]
      
      tt1 <- 1:length(ro)
      tt1 <- c(rev(tt1), (length(ro) + 1):length(sasa))
      keAu1 <- Kendall(sasa, tt1)
      varsA2 <- keAu1[[5]]
      pcA2 <- keAu1[[2]]
      
      S = nposAB - nnegAB
      Tau = S/npairsAB
      
      
      
    }
    else {
      
      if (rp1 == Aphase) {
        matriAB <- mat[1:NuA, (NuB + 1):(NuB + NuA)]
      }
      else {
        matriAB <- mat[(NuA + 1):(NuB + NuA), 1:NuB]
      }
      zero <- 0
      nposAB <- 0
      nnegAB <- 0
      ele <- 0
      
      #print(matriAB)
      
      for (i in 1:dim(matriAB)[1]) {
        for (k in 1:dim(matriAB)[2]) {
          
          
          if ((i == (dim(matriAB)[2] - k + 1)) && is.na(matriAB[i, 
                                                                k]) == FALSE) {
            zero = zero + 1
          }
          if ((matriAB[i, k] > 0) == TRUE && is.na(matriAB[i, 
                                                           k]) == FALSE) {
            nposAB = nposAB + 1
            
          }
          if (matriAB[i, k] < 0 && is.na(matriAB[i, k]) == 
              FALSE) {
            nnegAB = nnegAB + 1
          }
          if (is.na(matriAB[i, k]) == FALSE) {
            ele = ele + 1
          }
          
        }
      }
      
      ro <- rownames(matriAB)
      tre <- 1:length(ro)
      ke <- Kendall(ro, tre)
      
      
      npairsAB = ele - zero
      S = nposAB - nnegAB
      Tau = S/npairsAB
      varsf <- 0
      pcf <- 0
      varsA1 = pcA1 = varsA2 = (pcA2 <- 0)
    }
    
    vars <- (ke[[5]])
    SDs <- sqrt(vars)
    z = S/SDs
    pco <- (ke[[2]])
    pz <- 2 * pnorm(-abs(z))
    
    dd <- c(npairsAB, nposAB, nnegAB, S, Tau, SDs, vars, 
            z, pz, pco, varsf, pcf, varsA1, pcA1, varsA2, pcA2)
    
    return(dd)
  }
  
  
  ABpart <- findindices(compa, Aphase, Bphase, tri = FALSE)
  AApart <- findindices(compa, Aphase, Aphase, tri = TRUE)
  BBpart <- findindices(compa, Bphase, Bphase, tri = TRUE)
  
  vf <- ABpart[11]
  prf <- ABpart[12]
  
  vars_A1 <- ABpart[13]
  pc_A1 <- ABpart[14]
  vars_A2 <- ABpart[15]
  pc_A2 <- ABpart[16]
  
  
  ABpart <- ABpart[1:10]
  AApart <- AApart[1:10]
  BBpart <- BBpart[1:10]
  
  
  PartitionsOfMatrix <- matrix(, nrow = 11, ncol = 3)
  FullMatrix <- matrix(, nrow = 11, ncol = 1)
  TAU_U_Analysis <- matrix(, nrow = 11, ncol = 2)
  
  rownames(PartitionsOfMatrix) <- c("n_pairs", "n_pos", "n_neg", 
                                    "S", "Tau", "SDs", "VARs", "Z", "p_Z_based", "p_exact","r_effect_size")
  colnames(PartitionsOfMatrix) <- c("AvsB", "TrendA", "TrendB")
  
  rownames(FullMatrix) <- c("n_pairs", "n_pos", "n_neg", "S", 
                            "Tau", "SDs", "VARs", "Z", "p_Z_based)", "p_exact","r_effect_size")
  
  rownames(TAU_U_Analysis) <- c("n_pairs", "n_pos", "n_neg", 
                                "S", "Tau", "SDs", "VARs", "Z", "p_Z based", "p_exact","r_effect_size")
  colnames(TAU_U_Analysis) <- c("AvsBTrendB", "AvsBTrendBTrendA")
  
  PartitionsOfMatrix[1:10, 1] <- round(ABpart, 3)
  PartitionsOfMatrix[1:10, 2] <- round(AApart, 3)
  PartitionsOfMatrix[1:10, 3] <- round(BBpart, 3)
  PartitionsOfMatrix[11, ] <- sin(.5*pi*PartitionsOfMatrix["Tau",])
  
  
  FullMatrix[1:4, 1] <- apply(PartitionsOfMatrix[1:4, ], 1, 
                              sum)
  Tauf <- FullMatrix[4, 1]/FullMatrix[1, 1]
  SDf <- sqrt(vf)
  zf = FullMatrix[4, 1]/SDf
  pzf <- 2 * pnorm(-abs(zf))
  FullMatrix[5:10, 1] <- c(Tauf, SDf, vf, zf, pzf, prf)
  FullMatrix <- round(FullMatrix, 3)
  
  zz <- function(k) k[1] + k[3]
  TAU_U_Analysis[1:4, 1] <- apply(PartitionsOfMatrix[1:4, ], 
                                  1, zz)
  Taua1 <- TAU_U_Analysis[4, 1]/TAU_U_Analysis[1, 1]
  
  TAU_U_Analysis[1, 2] <- PartitionsOfMatrix[1, 1] + PartitionsOfMatrix[1,  2] + PartitionsOfMatrix[1, 3]
  TAU_U_Analysis[2, 2] <- PartitionsOfMatrix[2, 1] + PartitionsOfMatrix[2, 3] + PartitionsOfMatrix[3, 2]
  TAU_U_Analysis[3, 2] <- PartitionsOfMatrix[3, 1] + PartitionsOfMatrix[3,   3] + PartitionsOfMatrix[2, 2]
  TAU_U_Analysis[4, 2] <- TAU_U_Analysis[2, 2] - TAU_U_Analysis[3,  2]
  
  TAU_U_Analysis[5:8, 1] <- c(TAU_U_Analysis[4, 1]/TAU_U_Analysis[1, 1], sqrt(vars_A1), vars_A1, TAU_U_Analysis[4, 1]/sqrt(vars_A1))
  TAU_U_Analysis[5:8, 2] <- c(TAU_U_Analysis[4, 2]/TAU_U_Analysis[1,  2], sqrt(vars_A2), vars_A2, TAU_U_Analysis[4, 2]/sqrt(vars_A2))
  TAU_U_Analysis[9:10, 1] <- c(2 * pnorm(-abs(TAU_U_Analysis[8,  1])), pc_A1)
  TAU_U_Analysis[9:10, 2] <- c(2 * pnorm(-abs(TAU_U_Analysis[8, 2])), pc_A2)
  TAU_U_Analysis <- round(TAU_U_Analysis, 3)
  
  mat1 <- compa[, rev(seq.int(ncol(compa)))]
  mat1[lower.tri(mat1)] <- NA
  mat1 <- mat1[, rev(seq_len(ncol(compa)))]
  #print(mat1)
  
  FullMatrix[11, ] <- sin(.5*pi*FullMatrix["Tau",])
  TAU_U_Analysis[11, ] <- sin(.5*pi*TAU_U_Analysis["Tau",])
  
  cmd = list(PartitionsOfMatrix = PartitionsOfMatrix, FullMatrix = FullMatrix,
             TAU_U_Analysis = TAU_U_Analysis)#, matri = mat1)
  cmd=toJSON(cmd, collapse=" ", )
  cmd=gsub("\n", "", cmd)
  return(cmd)
  
  
}

writeLines(tauSystem())