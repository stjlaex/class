<xsl:stylesheet version='1.0' xmlns:xsl='http://www.w3.org/1999/XSL/Transform'>
<!-- xslt for subject reports presented one subject per page with with one to several 
	 assessment grades and a long written comment and a categories' table; 
	 there is an optional cover page-->

<xsl:output method='html' />

<xsl:template match='text()' />


<xsl:template match="html/body/div">
  <xsl:apply-templates select="student"/>
</xsl:template>

<xsl:template match="student">
<xsl:if test="coversheet='yes'">
  <div class='container'>
	<h1>Summer Report</h1>
	<table class='studenthead'>
	  <tr>
		<td>
		  <label>
			<xsl:value-of select="forename/label/text()" />
		  </label>
		  <xsl:value-of select="forename/value/text()" />
		</td>
		<td>
		  <label>
			<xsl:value-of select="surname/label/text()" />
		  </label>
		  <xsl:value-of select="surname/value/text()" />
		</td>
		<td>
		</td>
		<td></td>
		</tr>
		<tr>		
		<td>
		  <label>
			<xsl:value-of select="middlenames/label/text()" />
		  </label>
		  <xsl:value-of select="middlenames/value/text()" />
		</td>
		  <td>
		  <label>
			Form
		  </label>
		  <xsl:value-of select="registrationgroup/value/text()" />
		</td>
		<td></td>
		<td></td>
	  </tr>
	  <tr>
		<td><label>Date</label>
		  <xsl:value-of select="reports/publishdate/text()" />
		</td>
		<td></td>
		<td></td>
		</tr>
		<tr>
		<td><label>Number of Subjects in Report</label>
		<xsl:value-of select="count(reports/report)" />
		</td>
		<td></td>
		<td></td>
	  </tr>
	</table>

	<div id='tutor'>
	  Tutor
	  <xsl:apply-templates select="tutor"/>
	  <p class='blank'></p>
	  <p class='signature'>Signed</p>
	</div>
	<div id='year'>
	  Year Coordinator
	  <xsl:apply-templates select="year"/>
	  <p class='blank'></p>
	  <p class='signature'>Signed</p>
	</div>
	<div id='head'>
	  Head of Secondary
	  <xsl:apply-templates select="head"/>
	  <p class='signature'>Signed</p>
	</div>
  </div>
  <hr />
</xsl:if>

<xsl:apply-templates select="reports"/>
</xsl:template>

<xsl:template match="reports">
  <xsl:apply-templates select="report"/>
</xsl:template>


<xsl:template match="report">
  <div class="container">
	<h1>Subject:
	<xsl:choose>
	  <xsl:when test="component/value/text()!=' '">
		<xsl:value-of select="component/value/text()" />
	  </xsl:when>
	  <xsl:otherwise>
		<xsl:value-of select="subject/value/text()" />
	  </xsl:otherwise>
	</xsl:choose>
	</h1>

	<table class='studenthead'>
	  <tr>
		<td>
		  <label>
			Name:
		  </label>
		<xsl:value-of select="../../forename/value/text()" /></td>
		<td>
		  <xsl:value-of select="../../surname/value/text()" />
		</td>
		<td id='form'>
		  <label>
			Form:
		  </label>
		  <xsl:value-of select="../../registrationgroup/value/text()" />
		</td>
	  </tr>
	</table>


	<div class='grades' id='gradesleft'>
	  Summer Term's Work
	  <table>

		<xsl:if test="assessments/assessment/description/value='Exam Grade'">
		  <tr><th><label>Exam Grade</label></th><td>
		  <xsl:value-of select="assessments/assessment[description/value='Exam Grade']/result/value/text()" />  
		  </td></tr>
		</xsl:if>
		<xsl:if test="assessments/assessment/description/value='GCSE Exam Grade'">
		  <tr><th><label>IGCSE Grade</label></th><td>
		  <xsl:value-of select="assessments/assessment[description/value='GCSE Exam Grade']/result/value/text()" />  
		  </td></tr>
		</xsl:if>
		<tr><th><label>Effort</label></th><td>
		<xsl:value-of select="assessments/assessment[description/value='June Effort']/result/value/text()" />  
		<xsl:text>&#160; </xsl:text>
		</td></tr>
		<tr><th><label>Attainment</label></th><td>
  <xsl:value-of select="assessments/assessment[description/value='June Attainment']/result/value/text()" />  
  <xsl:text>&#160;</xsl:text>
		</td></tr>
	  </table>
	</div>
	<div class='grades' id='gradesright'>
	  Year's Work
	  <table>
		<tr><th><label>Final Mark</label></th><td>
		<xsl:value-of select="assessments/assessment[description/value='Final Mark']/result/value/text()" />  
		<xsl:text>&#160; </xsl:text>
		</td></tr>
		
	  </table>
	</div>

	<div class='spacer'>
	  <xsl:text></xsl:text>
	</div>


	<div class='criteria'>
	  <table>
		<tr>
		  <th>
		  </th>
		  <th>
			Good
		  </th>
		  <th>
			Satisfactory
		  </th>
		  <th>
			Poor
		  </th>
		</tr>
		<xsl:for-each select="comments/comment/categories/category">
		  <tr>
			<th>
			  <xsl:value-of select="label/text()" />
			</th>
			<td>
			  <xsl:choose>
				<xsl:when test="value='1'">
				  <xsl:text>X</xsl:text>
				</xsl:when>
				<xsl:otherwise>
				  <xsl:text>-</xsl:text>
				</xsl:otherwise>
			  </xsl:choose>
			</td>
			<td>
			  <xsl:choose>
				<xsl:when test="value='0'">
				  <xsl:text>X</xsl:text>
				</xsl:when>
				<xsl:otherwise>
				  <xsl:text>-</xsl:text>
				</xsl:otherwise>
			  </xsl:choose>
			</td>
			<td>
			  <xsl:choose>
				<xsl:when test="value='-1'">
				  <xsl:text>X</xsl:text>
				</xsl:when>
				<xsl:otherwise>
				  <xsl:text>-</xsl:text>
				</xsl:otherwise>
			  </xsl:choose>
			</td>
		  </tr>
		</xsl:for-each>
	  </table>
	</div>

	<div class='subcomment'>
	  Subject Specific Comments<br />
	  <xsl:apply-templates select="comments"/>
	  <xsl:text>&#160;</xsl:text>
	</div>

  </div>
  <hr />
</xsl:template>

<xsl:template match="comments">
  <xsl:apply-templates select="comment"/>
</xsl:template>

<xsl:template match="comment">
<div class="comment-text">
	<xsl:value-of select="text/value/text()" />
	<xsl:text>&#160;(</xsl:text>
	<xsl:value-of select="teacher/value/text()" />
	<xsl:text>)&#160;</xsl:text>
</div>
</xsl:template>


</xsl:stylesheet>
